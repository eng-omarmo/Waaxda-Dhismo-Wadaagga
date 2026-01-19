# Portal Registration - Step 1 Removal Summary

## ✅ Changes Completed

### Removed: Select Service Step (Step 1)
The standalone service selection page (`/portal/select-service`) has been removed from the registration flow.

### Updated Flow

**Before:**
```
Landing Page → /portal (Select Service) → /portal/info (Step 2) → ...
```

**After:**
```
Landing Page → /portal?serviceId=X → /portal/info (Step 1) → ...
```

### Implementation Details

1. **Controller Updated** (`SelfServiceController.php`)
   - `start()` method now **requires** `serviceId` parameter
   - If `serviceId` is not provided, redirects to landing page
   - If `serviceId` is invalid, redirects to landing page with error
   - Removed `select-service` view rendering
   - Logic remains the same: creates `PendingRegistration` and redirects to `portal.info`

2. **Legacy Route Support**
   - `storeService()` method now redirects to `portal.start` with serviceId (for backward compatibility)
   - Route `/portal/service` (POST) still exists but redirects

3. **Info View Updated** (`portal/info.blade.php`)
   - Step indicator updated: "Step 1 of 5" (was "Step 2 of 6")
   - Progress bar updated: 20% (was 33%)
   - "Back" button now links to landing page instead of portal.start

4. **Resume Functionality**
   - `resume()` method already redirects to `portal.info` ✅
   - No changes needed

## Logic Preserved

All existing logic remains intact:
- ✅ `PendingRegistration` creation with service_id
- ✅ Session management
- ✅ Resume token generation
- ✅ Service validation
- ✅ Error handling
- ✅ All subsequent steps work the same

## User Experience

**Before:** Users had to select service on a separate page  
**After:** Users select service on landing page, then go directly to info form

## Entry Points

1. **From Landing Page** (Primary):
   - Link: `/portal?serviceId=X`
   - Creates registration → Goes to `/portal/info`

2. **Direct Access** (Protected):
   - `/portal` without serviceId → Redirects to landing page
   - `/portal/info` without session → Redirects to landing page

## Backward Compatibility

- Old `/portal/service` POST route still works (redirects)
- Old `select-service.blade.php` view file still exists (not used)
- All existing links from landing page already use `serviceId` parameter ✅

## Files Modified

1. `app/Http/Controllers/SelfServiceController.php`
   - `start()` method updated to require serviceId
   - `storeService()` method updated to redirect

2. `resources/views/portal/info.blade.php`
   - Step labels updated (Step 1 of 5)
   - Back button updated

## Testing Checklist

- [ ] Landing page service links work correctly
- [ ] `/portal` without serviceId redirects to landing page
- [ ] `/portal?serviceId=X` creates registration and goes to info
- [ ] `/portal/info` works correctly with session
- [ ] Resume token functionality works
- [ ] All subsequent steps (details, docs, pay) work correctly

---

**Status:** ✅ Complete - Step 1 (Select Service) removed while preserving all logic
