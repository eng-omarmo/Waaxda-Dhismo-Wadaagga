<?php

namespace App\Http\Controllers;

use App\Models\Project;
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
}
