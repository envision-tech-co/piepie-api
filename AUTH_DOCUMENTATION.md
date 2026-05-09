# PipPip Authentication System Documentation

## Overview

Complete authentication system for PipPip - an on-demand automotive services platform with three user types:
- **Customer** - Mobile app users requesting services
- **Service Provider** - Mechanics/tow truck drivers accepting jobs
- **Admin** - Web portal operators managing the platform

## Technology Stack

- **Framework**: Laravel 11
- **Authentication**: Laravel Sanctum (API tokens for mobile) + Session-based (Admin web)
- **OTP Delivery**: Configurable SMS driver (currently logging for development)
- **Multi-language**: Support for English (en), Arabic (ar), Kurdish (ku)

## Database Schema

### Tables Created

1. **customers**
   - id, name, phone (unique), profile_photo, language, is_active, phone_verified_at, timestamps

2. **service_providers**
   - id, name, phone (unique), profile_photo, vehicle_type, service_speciality, id_document_path, language, status (pending/approved/rejected/suspended), is_online, overall_rating, total_jobs, phone_verified_at, timestamps

3. **admins**
   - id, name, email (unique), password, role (super_admin/operations/finance), is_active, timestamps

4. **otp_codes**
   - id, phone, otp_code (hashed), type (customer/provider), expires_at, used_at, attempts, timestamps

5. **personal_access_tokens** (Sanctum)

## API Endpoints

### Customer Authentication

#### 1. Send OTP
```
POST /api/customer/auth/send-otp
Content-Type: application/json
Accept-Language: en|ar|ku

Body:
{
  "phone": "+9647501234567"
}

Response:
{
  "success": true,
  "message": "OTP sent successfully",
  "data": {
    "expires_in": 600
  }
}
```

**Rate Limit**: 5 requests per 10 minutes per phone

#### 2. Verify OTP
```
POST /api/customer/auth/verify-otp
Content-Type: application/json

Body:
{
  "phone": "+9647501234567",
  "otp": "123456"
}

Response:
{
  "success": true,
  "message": "OTP verified successfully",
  "data": {
    "token": "1|abc123...",
    "token_type": "Bearer",
    "customer": {
      "id": 1,
      "name": null,
      "phone": "+9647501234567",
      "profile_photo_url": null,
      "language": "en",
      "is_active": true,
      "created_at": "2026-05-09T08:00:00.000000Z"
    },
    "is_new": true
  }
}
```

**Rate Limit**: 10 requests per minute

#### 3. Complete Profile
```
POST /api/customer/auth/complete-profile
Authorization: Bearer {token}
Content-Type: multipart/form-data

Body:
- name: "John Doe"
- language: "en"
- profile_photo: [file] (optional, max 2MB)

Response:
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "customer": { ... }
  }
}
```

#### 4. Get Current Customer
```
GET /api/customer/auth/me
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "customer": { ... }
  }
}
```

#### 5. Logout
```
POST /api/customer/auth/logout
Authorization: Bearer {token}

Response:
{
  "success": true,
  "message": "Logged out successfully"
}
```

### Service Provider Authentication

#### 1. Send OTP
```
POST /api/provider/auth/send-otp
Content-Type: application/json

Body:
{
  "phone": "+9647509876543"
}
```

#### 2. Verify OTP
```
POST /api/provider/auth/verify-otp
Content-Type: application/json

Body:
{
  "phone": "+9647509876543",
  "otp": "123456"
}

Response:
{
  "success": true,
  "message": "OTP verified successfully",
  "data": {
    "token": "2|xyz789...",
    "token_type": "Bearer",
    "provider": {
      "id": 1,
      "name": null,
      "phone": "+9647509876543",
      "vehicle_type": null,
      "service_speciality": null,
      "status": "pending",
      "is_online": false,
      "overall_rating": 0.00,
      "total_jobs": 0,
      "profile_photo_url": null,
      "language": "en",
      "created_at": "2026-05-09T08:00:00.000000Z"
    },
    "is_new": true
  }
}
```

#### 3. Register (Complete Profile)
```
POST /api/provider/auth/register
Authorization: Bearer {token}
Content-Type: multipart/form-data

Body:
- name: "Ahmed Mechanic"
- vehicle_type: "car" (car|motorcycle|truck|other)
- service_speciality: "Engine Repair"
- language: "en" (optional)
- id_document: [file] (required, max 5MB, jpg|jpeg|png|pdf)

Response:
{
  "success": true,
  "message": "Registration submitted successfully. Awaiting admin approval.",
  "data": {
    "provider": { ... }
  }
}
```

**Note**: Provider status remains "pending" until admin approves.

#### 4. Get Current Provider
```
GET /api/provider/auth/me
Authorization: Bearer {token}
```

#### 5. Logout
```
POST /api/provider/auth/logout
Authorization: Bearer {token}
```

### Admin Authentication (Web)

#### 1. Login Page
```
GET /admin/login
```
Returns HTML login form

#### 2. Login
```
POST /admin/login
Content-Type: application/x-www-form-urlencoded

Body:
- email: admin@pippip.com
- password: Admin@123456
- remember: on (optional)

Success: Redirects to /admin/dashboard
Failure: Redirects back with errors
```

#### 3. Dashboard
```
GET /admin/dashboard
Middleware: auth:admin
```

#### 4. Logout
```
POST /admin/logout
Middleware: auth:admin

Redirects to /admin/login
```

## Default Admin Credentials

```
Email: admin@pippip.com
Password: Admin@123456
Role: super_admin
```

## Configuration

### Environment Variables (.env)

```env
# OTP Configuration
OTP_EXPIRY_MINUTES=10
OTP_MAX_ATTEMPTS=3
OTP_LENGTH=6

# SMS Configuration
SMS_DRIVER=log
SMS_FROM=PipPip
```

### Supported Languages

Set via `Accept-Language` header:
- `en` - English (default)
- `ar` - Arabic
- `ku` - Kurdish

## Security Features

1. **OTP Security**
   - Codes are hashed using bcrypt before storage
   - 10-minute expiration
   - Maximum 3 verification attempts
   - Previous unused OTPs are invalidated when new one is generated

2. **Rate Limiting**
   - Send OTP: 5 requests per 10 minutes
   - Verify OTP: 10 requests per minute

3. **Token-based Authentication**
   - Sanctum tokens with abilities (customer/provider)
   - Tokens can be revoked on logout

4. **Provider Status Checks**
   - Suspended/rejected providers cannot authenticate
   - Pending providers can register but need approval

5. **File Upload Security**
   - Files stored in local disk (not public)
   - Size limits: 2MB for profile photos, 5MB for documents
   - Allowed formats validated

## Middleware

### SetLocale
- Global middleware
- Reads `Accept-Language` header
- Sets application locale (en/ar/ku)

### CheckProviderStatus
- Applied to provider routes requiring approval
- Returns 403 if provider status is not "approved"

## API Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": { ... }
}
```

### HTTP Status Codes
- 200: Success
- 401: Unauthenticated
- 403: Forbidden (inactive/suspended account)
- 422: Validation error
- 404: Resource not found

## Testing with Postman

Import `postman_collection.json` from the project root.

### Variables to Set:
- `base_url`: http://localhost:8000
- `customer_token`: (auto-populated after verify-otp)
- `provider_token`: (auto-populated after verify-otp)

### Testing Flow:

**Customer Flow:**
1. Send OTP → Check logs for OTP code
2. Verify OTP → Save token
3. Complete Profile
4. Get Me
5. Logout

**Provider Flow:**
1. Send OTP → Check logs for OTP code
2. Verify OTP → Save token
3. Register with documents
4. Get Me (status will be "pending")
5. Admin approves (manual DB update for now)
6. Logout

**Admin Flow:**
1. Visit http://localhost:8000/admin/login
2. Login with default credentials
3. Access dashboard

## File Storage

### Customer Profile Photos
```
storage/app/customer-photos/{filename}
```

### Provider Documents
```
storage/app/provider-docs/{provider_id}/{filename}
```

**Note**: Files are stored in local disk for security. Use `Storage::url()` to generate URLs.

## Development Notes

### Viewing OTP Codes
OTP codes are logged to `storage/logs/laravel.log`:
```
[2026-05-09 08:00:00] local.INFO: OTP for +9647501234567: 123456
```

### Database Inspection
```bash
php artisan tinker

# Check customers
App\Models\Customer::all();

# Check providers
App\Models\ServiceProvider::all();

# Check admins
App\Models\Admin::all();

# Check OTP codes
App\Models\OtpCode::all();
```

### Approve a Provider (Manual)
```bash
php artisan tinker

$provider = App\Models\ServiceProvider::find(1);
$provider->update(['status' => 'approved']);
```

## Next Steps

1. **Implement Real SMS**: Replace log driver with actual SMS service (Twilio, AWS SNS, etc.)
2. **Admin Panel**: Build full CRUD for managing customers, providers, and approvals
3. **Password Reset**: Add forgot password flow for admins
4. **Email Notifications**: Notify providers when approved/rejected
5. **Profile Photo URLs**: Configure public disk or signed URLs for file access
6. **API Documentation**: Generate OpenAPI/Swagger docs
7. **Unit Tests**: Add PHPUnit tests for authentication flows

## Troubleshooting

### Issue: "Unauthenticated" error
- Ensure token is included in Authorization header: `Bearer {token}`
- Check token hasn't been revoked
- Verify correct guard is being used

### Issue: OTP not working
- Check `storage/logs/laravel.log` for generated OTP
- Verify OTP hasn't expired (10 minutes)
- Check attempts haven't exceeded limit (3)

### Issue: File upload fails
- Verify file size limits
- Check file MIME types
- Ensure storage directory is writable

### Issue: Admin login redirects to /login
- Check auth guard is set to 'admin'
- Verify admin exists in database
- Check is_active flag is true

## Support

For issues or questions, contact the development team.

---

**Built with Laravel 11 | Sanctum | Tailwind CSS**
