<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\BusinessLicense;
use App\Models\ServiceRequest;
use App\Models\Organization;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class ServiceTrackingController extends Controller
{
    public function show()
    {
        return view('track');
    }

    public function lookup(Request $request)
    {
        $request->validate([
            'reference' => ['required', 'uuid'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $key = 'track:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 15)) {
            return back()->withErrors(['reference' => __('Too many attempts. Please try again later.')])->withInput();
        }

        try {
            $reference = $request->string('reference')->toString();
            $email = $request->string('email')->toString();

            $cacheKey = 'track:project:'.$reference;
            $result = Cache::remember($cacheKey, now()->addSeconds(60), function () use ($reference) {
                return Project::query()->select(['id', 'status', 'updated_at', 'registrant_email'])->find($reference);
            });

            $success = false;
            $matchedEmail = false;
            $data = null;

            if ($result) {
                $success = true;
                $matchedEmail = $email !== '' && strcasecmp($email, $result->registrant_email) === 0;

                $nextMilestone = match ($result->status) {
                    'Draft' => __('Complete registration details and submit'),
                    'Submitted' => __('Await officer review'),
                    'Approved' => __('Proceed to permits, buildings, and units'),
                    default => __('Await updates'),
                };

                $data = [
                    'reference' => $result->id,
                    'status' => $result->status,
                    'updated_at' => $result->updated_at,
                    'next_milestone' => $nextMilestone,
                    'contact' => [
                        'email' => 'support@ipams.gov.so',
                        'phone' => '061-0000000',
                    ],
                    'email_provided' => $email !== '',
                    'email_matched' => $matchedEmail,
                ];
            }

            DB::table('service_search_logs')->insert([
                'ref_type' => $result ? 'project' : null,
                'ref_id' => $reference,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'was_success' => $success,
                'matched_email' => $matchedEmail,
                'created_at' => now(),
            ]);

            if (! $result) {
                return back()->withErrors(['reference' => __('No matching record found')])->withInput();
            }

            return view('track', ['data' => $data]);
        } catch (\Throwable $e) {
            DB::table('service_search_logs')->insert([
                'ref_type' => null,
                'ref_id' => $request->string('reference')->toString(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'was_success' => false,
                'matched_email' => false,
                'created_at' => now(),
            ]);

            return back()->withErrors(['reference' => __('System unavailable. Please try again later.')])->withInput();
        } finally {
            RateLimiter::hit($key, 60);
        }
    }

    public function lookupByPhone(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:50'],
        ]);

        $key = 'track_phone:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 15)) {
            return back()->withErrors(['phone' => __('Too many attempts. Please try again later.')])->withInput();
        }

        try {
            $phone = $request->string('phone')->toString();
            $cacheKey = 'track:phone:'.$phone;
            $data = Cache::remember($cacheKey, now()->addSeconds(60), function () use ($phone) {
                $items = [];

                $requests = ServiceRequest::with('service')->where('user_phone', $phone)->orderBy('updated_at', 'desc')->get();
                foreach ($requests as $r) {
                    $items[] = [
                        'type' => 'Service Request',
                        'name' => $r->service?->name ?? 'Service',
                        'status' => ucfirst($r->status),
                        'updated_at' => $r->updated_at,
                        'next_milestone' => match ($r->status) {
                            'pending' => __('Await payment verification'),
                            'verified' => __('Certificate available or processing complete'),
                            'rejected' => __('Contact support'),
                            'discrepancy' => __('Resolve payment discrepancy'),
                            default => __('Await updates'),
                        },
                    ];
                }

                $projects = Project::where('registrant_phone', $phone)->orderBy('updated_at', 'desc')->get();
                foreach ($projects as $p) {
                    $items[] = [
                        'type' => 'Project',
                        'name' => 'Project #'.$p->id,
                        'status' => $p->status,
                        'updated_at' => $p->updated_at,
                        'next_milestone' => match ($p->status) {
                            'Draft' => __('Complete registration details and submit'),
                            'Submitted' => __('Await officer review'),
                            'Approved' => __('Proceed to permits, buildings, and units'),
                            default => __('Await updates'),
                        },
                    ];
                }

                $licenses = BusinessLicense::where('registrant_phone', $phone)->orderBy('updated_at', 'desc')->get();
                foreach ($licenses as $l) {
                    $items[] = [
                        'type' => 'Business License',
                        'name' => $l->company_name,
                        'status' => ucfirst($l->status),
                        'updated_at' => $l->updated_at,
                        'next_milestone' => match ($l->status) {
                            'approved' => __('Download license or view details'),
                            'rejected' => __('See comments and reapply if needed'),
                            default => __('Await verification'),
                        },
                    ];
                }

                $orgs = Organization::where('contact_phone', $phone)->orderBy('updated_at', 'desc')->get();
                foreach ($orgs as $o) {
                    $items[] = [
                        'type' => 'Organization',
                        'name' => $o->name,
                        'status' => ucfirst($o->status ?? 'pending'),
                        'updated_at' => $o->updated_at,
                        'next_milestone' => __('Check organization approval status'),
                    ];
                }

                $apartments = Apartment::where('contact_phone', $phone)->orderBy('updated_at', 'desc')->get();
                foreach ($apartments as $a) {
                    $items[] = [
                        'type' => 'Apartment',
                        'name' => $a->name,
                        'status' => 'Active',
                        'updated_at' => $a->updated_at,
                        'next_milestone' => __('View ownership or transfer details'),
                    ];
                }

                usort($items, fn ($x, $y) => strcmp((string) $y['updated_at'], (string) $x['updated_at']));

                return [
                    'phone' => $phone,
                    'items' => $items,
                    'contact' => [
                        'email' => 'support@ipams.gov.so',
                        'phone' => '061-0000000',
                    ],
                ];
            });

            DB::table('service_search_logs')->insert([
                'ref_type' => 'phone',
                'ref_id' => $request->string('phone')->toString(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'was_success' => true,
                'matched_email' => false,
                'created_at' => now(),
            ]);

            return view('track', ['phoneData' => $data]);
        } catch (\Throwable $e) {
            DB::table('service_search_logs')->insert([
                'ref_type' => 'phone',
                'ref_id' => $request->string('phone')->toString(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'was_success' => false,
                'matched_email' => false,
                'created_at' => now(),
            ]);

            return back()->withErrors(['phone' => __('System unavailable. Please try again later.')])->withInput();
        } finally {
            RateLimiter::hit($key, 60);
        }
    }
}
