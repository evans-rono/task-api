# Task Management API

A Laravel + MySQL Task Management API built for the internship take-home assignment.

## Features

- Create tasks
- List tasks with optional status filter
- Update task status with strict progression
- Delete only completed tasks
- Daily report grouped by priority and status

## Tech Stack

- PHP 8.2
- Laravel 12
- MySQL
- Eloquent ORM

## Setup Instructions

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env`
4. Configure database
5. Run `php artisan migrate`
6. Run `php artisan db:seed`
7. Run `php artisan serve`

## API Endpoints

- POST `/api/tasks`
- GET `/api/tasks`
- PATCH `/api/tasks/{id}/status`
- DELETE `/api/tasks/{id}`
- GET `/api/tasks/report?date=YYYY-MM-DD`

## Database

- MySQL
- SQL dump included: `task_management.sql`