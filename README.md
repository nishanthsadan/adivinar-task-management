# Task Management System

A production-ready task management application built with Laravel 12, featuring clean architecture, Repository Pattern, AI-generated task summaries via OpenAI, role-based access control, and a fully dark-themed responsive UI.



## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 12 |
| Language | PHP 8.2+ |
| Database | MySQL 8.0+ |
| Frontend | Blade Templates |
| Styling | Custom CSS (dark theme, `tasks.css`) |
| Charts | Chart.js 4 |
| Authentication | Laravel Breeze + Sanctum |
| AI Provider | OpenAI GPT-3.5 Turbo (mock fallback included) |
| Cache | Laravel File Cache (stats, monthly data) |
| API Auth | Laravel Sanctum (token-based) |

---

## Architecture Overview

The application enforces a strict **four-layer architecture**. No layer is allowed to skip a layer below it.

```
HTTP Request
    │
    ▼
Controller          — receives request, delegates, returns response
    │
    ▼
Service             — business logic, transactions, orchestration
    │
    ▼
Repository          — all database queries, caching
    │
    ▼
Eloquent Model      — schema definition, casts, scopes, relationships
```

### Key Rules

- **Controllers** never call Eloquent models directly. They only call Services.
- **Services** never call Eloquent models directly. They only call Repositories.
- **Repositories** are the only layer that touches Eloquent. All queries live here.
- **Interfaces** define the Repository contracts. Implementations are swapped via the IoC container, making the data layer replaceable without touching business logic.

### Dependency Injection via IoC

Interfaces are bound to implementations in `RepositoryServiceProvider`:

```php
$this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
$this->app->bind(UserRepositoryInterface::class, UserRepository::class);
```

This means any service that type-hints `TaskRepositoryInterface` automatically receives `TaskRepository`. Swapping to a different data source (e.g. an API or a NoSQL driver) requires only changing this binding.

---

## Authentication & Roles

Authentication is handled by **Laravel Breeze** (session-based for web) and **Laravel Sanctum** (token-based for API).

### Roles

| Role | Permissions |
|---|---|
| `admin` | Full access — create, view, edit, delete any task, see all users in sidebar |
| `user` | View and edit only their own assigned tasks, cannot create or delete |

Role is stored as an enum column on the `users` table and cast automatically:

```php
// app/Enums/UserRole.php
enum UserRole: string
{
    case Admin = 'admin';
    case User  = 'user';
}
```

The `isAdmin()` helper on the `User` model is used throughout policies and controllers:

```php
public function isAdmin(): bool
{
    return $this->role === UserRole::Admin;
}
```

### Policy Rules

All authorization is handled in `TaskPolicy`. Controllers call `$this->authorize()` — never inline `if` checks.

| Method | Admin | User |
|---|---|---|
| `viewAny` | ✅ | ✅ (own tasks only) |
| `view` | ✅ | ✅ if `assigned_to = auth user` |
| `create` | ✅ | ❌ |
| `update` | ✅ | ✅ if `assigned_to = auth user` |
| `delete` | ✅ | ❌ |

---

## API Endpoints

All API endpoints are protected by Sanctum token authentication. Include the token in every request header:

```
Authorization: Bearer <your-token>
Accept: application/json
```

### Authentication

#### POST `/api/login`
Obtain a Sanctum API token.

**Request body:**
```json
{
    "email": "admin@m.com",
    "password": "123456"
}
```

**Response:**
```json
{
    "token": "1|abc123xyz...",
    "user": {
        "id": 1,
        "name": "Admin",
        "email": "admin@m.com",
        "role": "admin"
    }
}
```

**Usage:** Call this endpoint first to retrieve a token. Store the token and pass it as `Authorization: Bearer <token>` in all subsequent requests.

---

#### POST `/api/logout`
Revoke the current token.

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{ "message": "Logged out." }
```

---

### Tasks

#### GET `/api/tasks`
Retrieve a paginated list of tasks. Admins see all tasks; regular users see only tasks assigned to them.

**Query parameters (all optional):**

| Parameter | Type | Description |
|---|---|---|
| `status` | string | Filter by `pending`, `in_progress`, or `completed` |
| `priority` | string | Filter by `low`, `medium`, or `high` |
| `search` | string | Search task titles (partial match) |

**Response:**
```json
{
    "data": [
        { ... }
    ],
    "links": { ... },
    "meta": { ... }
}
```

---

#### POST `/api/tasks`
Create a new task. Admin only. AI summary is generated automatically after creation.

**Request body:**
```json
{
    "title": "Develop API Endpoints",
    "description": "Build and document all REST endpoints.",
    "priority": "high",
    "status": "pending",
    "due_date": "2025-12-31",
    "assigned_to": 2
}
```

**Response:** `201 Created` — returns the created task as a `TaskResource`.

---

#### PATCH `/api/tasks/{id}/status`
Update only the status of a task. Useful for kanban-style status toggling.

**Request body:**
```json
{ "status": "completed" }
```

**Response:** `200 OK` — returns the updated task as a `TaskResource`.

**Usage:** Call this when a user drags a task card to a new column or clicks a status toggle. It only updates the `status` field without triggering an AI regeneration, keeping the operation lightweight.

---

#### GET `/api/tasks/{id}/ai-summary`
Retrieve the current AI-generated summary and suggested priority for a specific task.

**Response:**
```json
{
    "ai_summary": "This high-priority task requires immediate attention. Ensure all dependencies are resolved before the deadline.",
    "ai_priority": "high"
}
```

**Usage:** Use this endpoint to display the AI analysis panel in a single-page context without reloading the full task resource.

---

## Installation

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+ and npm
- MySQL 8.0+

### Step-by-step setup

```bash
# 1. Clone the repository
git clone https://github.com/your-username/task-management.git
cd task-management

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies and build frontend assets
npm install
npm run build

# 4. Copy the environment file
cp .env.example .env

# 5. Generate the application key
php artisan key:generate

# 6. Configure your database in .env
# Set DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 7. Set up the API routes (installs Sanctum + creates personal_access_tokens table)
php artisan install:api

# 8. Run all migrations
php artisan migrate

# 9. Seed the database with default users
php artisan db:seed


# 10. Start the development server
php artisan serve
```

The application will be available at `http://localhost:8000`.

### Development asset watching

During development, run Vite in watch mode instead of step 3:

```bash
npm run dev
```

---

## User Seeding

Running `php artisan db:seed` creates two default users:

| Name | Email | Password | Role |
|---|---|---|---|
| Admin | `admin@m.com` | `password` | `admin` |
| testuser01 | `testuser01@m.com` | `123456` | `user` |
| testuser02 | `testuser01@m.com` | `123456` | `user` |

**Admin** has full access to create, edit, and delete any task, and sees all tasks across all users.

**Regular User** can only see and edit tasks assigned to them. They cannot create or delete tasks.


---

## AI Integration

AI summaries are generated by `AIService` using the OpenAI Chat Completions API.

### Prompt Template

```
Task: "{title}". Description: "{description}". Due: {due_date}.
Status: {status}. Priority: {priority}.
Return ONLY JSON with keys ai_summary and ai_priority (low|medium|high).
```

### When AI is triggered

| Action | AI triggered |
|---|---|
| Task created | ✅ Always — generates initial summary |
| Task title updated | ✅ Summary regenerated |
| Task description updated | ✅ Summary regenerated |
| Status or priority changed only | ❌ No regeneration — not needed |
| Regenerate button clicked | ✅ On demand |

### Mock fallback

If `OPENAI_API_KEY` is empty or the API call fails, `AIService` returns a safe mock response based on the task's existing priority. This means the application works fully without an API key during development.

```php
// Mock response when no API key is set
[
    'ai_summary'  => 'Task "{title}" requires attention. Priority: {priority}.',
    'ai_priority' => $task->priority->value,
]
```


---


## Security Measures

### Authentication
- Web routes are protected by `auth` and `verified` middleware via Laravel Breeze.
- API routes are protected by `auth:sanctum` middleware. Tokens are scoped per user and can be revoked individually.

### Authorization
- Every controller action calls `$this->authorize()` before acting. This checks `TaskPolicy` which enforces role-based access rules.
- Blade views use `@can` directives so action buttons (Edit, Delete, New Task) are never shown to unauthorized users — not just blocked server-side.

### Input Validation
- All create and update requests go through dedicated `FormRequest` classes (`StoreTaskRequest`, `UpdateTaskRequest`) with strict validation rules.
- API endpoints use inline `$request->validate()` with enum-based value restrictions.

### Mass Assignment Protection
- All models define explicit `$fillable` arrays. No `$guarded = []` is used anywhere.

### CSRF Protection
- All state-changing web forms include `@csrf`. The API uses Sanctum token authentication which is CSRF-exempt by design.

### AI Response Sanitization
- AI response content is parsed as JSON and validated. Only whitelisted fields (`ai_summary`, `ai_priority`) are extracted.
- `strip_tags()` is applied to `ai_summary` before saving, preventing any HTML or script injection from the AI response.
- `ai_priority` is validated against the allowed enum values before use. Unknown values fall back to the task's existing priority.

### Error Handling in AIService
- All OpenAI API calls are wrapped in `try/catch`. Any exception (network failure, timeout, malformed response) falls back to the mock response — the application never crashes due to an AI failure.

---

## Environment Variables

Key variables to configure in `.env`:

```env
# Application
APP_NAME="Task Management"
APP_ENV=local
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=your_password


# Cache
CACHE_STORE=file

# OpenAI — leave empty to use mock fallback
OPENAI_API_KEY=sk-your-key-here

```

---

## Screenshots

•	Dashboard (screenshots/Dashboard-1.png, Dashboard-2.png)
•	Task List (screenshots/TaskList.png)
•	Task Edit (screenshots/TaskEdit.png)
•	Task Details (screenshots/TaskDetail+AI-Summary.png)

---


