<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Laboratory Activity 2 — RBAC

## Laboratory Activity 2 — RBAC

### 1. Project Title and Short Description

**Project Title:** SNSU Faculty Consultation & Appointment System (FCAS)

**Description:** FCAS is a Laravel web application that allows students to book faculty consultations and appointments. It includes separate modules for students, faculty members, and administrators. Role-Based Access Control (RBAC) is implemented to restrict access to routes according to the authenticated user's role.

### 2. Selected Approach

**Approach: Dynamic Middleware**

The application uses a custom `CheckRole` middleware that accepts a role parameter. The middleware is applied to route groups using the `role` alias, such as `role:admin`, `role:faculty`, and `role:student`.

### 3. Role-and-Permission Table

| Role | Permissions |
|---|---|
| Admin | Access the admin dashboard, manage users, approve or reject registrations, manage appointments, and handle support tickets. |
| Faculty | Access the faculty dashboard, manage schedules, respond to consultation requests, manage announcements, and handle support tickets. |
| Student | Access the student dashboard, view faculty availability, book appointments, view consultation logs, read announcements, and submit support tickets. |

### 4. Middleware Implementation

- **Middleware class:** `App\Http\Middleware\CheckRole`
- **Middleware alias:** `role`
- **Alias registration:** `bootstrap/app.php`
- **Middleware logic:** `app/Http/Middleware/CheckRole.php`
- **Protected routes:** `routes/web.php`

The application protects its main role-specific route groups with the `auth` and `role` middleware.

Examples of protected routes:
- Admin: `/admin/dashboard`
- Faculty: `/faculty/dashboard`
- Student: `/student/dashboard`

### 5. How the Middleware Authorizes Requests

The `CheckRole` middleware first checks whether the user is authenticated. If the user is not logged in, the middleware redirects the request to the login page. It then retrieves the authenticated user's role and compares it with the role required by the route. If the roles do not match, the application returns a 403 Unauthorized action response; otherwise, the request continues to the intended destination. This enforces authorization on the server side.

### 6. Evidence Screenshots

The following screenshots demonstrate the RBAC implementation and authorization results:

1. [Admin Allowed](evidence/01-admin-allowed.png)
2. [Other Role Allowed](evidence/02-other-role-allowed.png)
3. [Role Access Denied](evidence/03-role-denied.png)
4. [Guest Access Blocked](evidence/04-guest-blocked.png)
5. [Restricted Action Denied](evidence/05-restricted-action-denied.png)

### 7. Reflection

One challenge in implementing RBAC was ensuring that each user role could access only its designated routes. Creating and registering the custom middleware required careful configuration and testing. Server-side authorization is necessary because users may attempt to access restricted URLs directly instead of navigating through the application's interface. By checking authentication and roles on the server, the application helps prevent unauthorized access to protected features.