# CHANGELOG

## [Day 9] - 2025-11-04
### Added
- User model scope: `scopeWhereEmail`
- `Auth\PasswordController`
- Form Requests: `ForgotPasswordRequest`, `PasswordResetRequest`
- Services: `ForgotPasswordService`, `ResetPasswordService`

### Changed
- OTP folder restructured to `app/Services/Auth/Otp/`
  - `BaseOtpService.php`
  - `LoginOtpService.php`
  - `RegisterOtpService.php`

