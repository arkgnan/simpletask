# SimpleTask - Laravel Task Management Application

This project is a small web application that allows users to manage a list of tasks using Laravel.

## Features

1.  **User Authentication**: User registration and login functionality.
2.  **Task Management**: Full CRUD (Create, Read, Update, Delete) operations for tasks.
3.  **Responsive UI**: Modern and responsive user interface powered by Tailwind CSS.
4.  **Front-end Logic**: Combination of Laravel Blade and JavaScript for interactive elements.
5.  **Weather Report Integration**: Integrates Current Weather Report using OpenWeather API upon user login (displayed on the dashboard).
6.  **API Security**: Protected API endpoints secured using OAuth 2.0 (Laravel Passport) for flexible token authentication.

## Local Setup Guide

Follow these steps to get the SimpleTask application running on your local machine.

### Prerequisites

*   PHP >= 8.3
*   Composer
*   Node.js & npm (or Yarn)
*   A database (MySQL, PostgreSQL, SQLite, etc.)
*   Git

### Installation Steps

1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/arkgnan/tasklist.git # Replace with your actual repository URL
    cd tasklist
    ```

2.  **Install Composer Dependencies:**
    ```bash
    composer install
    ```

3.  **Install Node Dependencies & Compile Assets:**
    ```bash
    npm install
    npm run dev # or npm run build for production assets
    ```

4.  **Copy Environment File:**
    ```bash
    cp .env.example .env
    ```

5.  **Generate Application Key:**
    ```bash
    php artisan key:generate
    ```

6.  **Configure Database:**
    Open your `.env` file and update the database connection details:
    ```
    DB_CONNECTION=pgsql # or mysql, sqlite
    DB_HOST=127.0.0.1
    DB_PORT=5432 # or 3306 for mysql
    DB_DATABASE=tasklist
    DB_USERNAME=your_db_user
    DB_PASSWORD=your_db_password
    ```

7.  **Setup Laravel Passport (for API Authentication):**
    ```bash
    php artisan passport:install
    ```
    This will create encryption keys and default OAuth clients.

8.  **Configure Passport Guard in `config/auth.php`:**
    Ensure your `api` guard uses the `passport` driver for OAuth 2.0
    ```php
    // config/auth.php
    'guards' => [
        // ...
        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
    ],
    ```
    And ensure `AppServiceProvider.php` has `Passport::enablePasswordGrant();` in its `boot` method.

9. **Link Storage (if applicable):**
    ```bash
    php artisan storage:link
    ```

10. **Start the Development Server:**
    ```bash
    php artisan serve
    ```
    Access the application at `http://localhost:8000` (or the URL provided by `php artisan serve`).

## Application Routes

### Web Routes (`routes/web.php`)

These routes handle the traditional web interface, authentication (managed by `auth.php`), and resource management for tasks.

*   `GET /`: Redirects to `/dashboard`.
*   `GET /dashboard`: Displays the main dashboard. Requires authentication (`auth` middleware).
*   `GET /login`: Displays the login form.
*   `POST /login`: Handles user login.
*   `GET /register`: Displays the registration form.
*   `POST /register`: Handles user registration.
*   `GET|POST|PUT|DELETE /task`: Resource routes for `TaskController` (index, create, store, show, edit, update, destroy). These are for the Blade views.
    *   `GET /task`: List all tasks.
    *   `GET /task/create`: Show form to create a new task.
    *   `POST /task`: Store a new task.
    *   `GET /task/{task}`: Show details of a specific task.
    *   `GET /task/{task}/edit`: Show form to edit a specific task.
    *   `PUT /task/{task}`: Update a specific task.
    *   `DELETE /task/{task}`: Delete a specific task.
*   `/auth.php`: Includes all authentication-related routes (login, register, password reset, etc.).

### API Routes (`routes/api.php`)

These routes expose JSON APIs for various functionalities, protected by both Laravel Sanctum (for first-party clients) and Laravel Passport (for OAuth 2.0 clients).

*   **Public Auth Routes (`/api/auth`):**
    *   `POST /api/auth/register`: Register a new user.
    *   `POST /api/auth/login`: Authenticate a user and issue a token (Sanctum token).

*   **Protected Auth Routes (`/api/auth` - requires `auth:api`):**
    *   `POST /api/auth/logout`: Log out the authenticated user.
    *   `GET /api/auth/me`: Get details of the authenticated user.

*   **Task Management API (`/api/task` - requires `auth:api`):**
    These endpoints allow external clients or your own SPA to manage tasks programmatically. They are protected by `auth:api`, which uses the Passport driver and can validate both Passport and Sanctum tokens.
    *   `GET /api/task`: List all tasks.
    *   `POST /api/task`: Create a new task.
    *   `GET /api/task/export`: Export tasks (e.g., to Excel).
    *   `GET /api/task/chart`: Get chart data for tasks.
    *   `GET /api/task/{task}`: Show details of a specific task.
    *   `PUT /api/task/{task}`: Update a specific task.
    *   `DELETE /api/task/{task}`: Delete a specific task.

*   **Weather Update API (`/api/weather/update`):**
    *   `POST /api/weather/update`: Updates weather information. This endpoint is specifically designed to be hit from the dashboard and is protected by `web` middleware (session-based authentication).

## OpenWeather API Integration

To enable the weather report feature:

1.  Obtain an API key from [OpenWeather](https://openweathermap.org/api).
2.  Add your API key to your `.env` file:
    ```
    OPENWEATHER_API_KEY=your_openweather_api_key_here
    ```
