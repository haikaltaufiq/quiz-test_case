# Quiz - Test Case

---

## Prerequisites

Before setting up the project locally, ensure you have the following environments installed and configured via **XAMPP**:

- **PHP:** `^8.3`
- **Database:** MariaDB / MySQL (Included in XAMPP)
- **Composer:** `^2.x`
- **Node.js & NPM:** `^20.x` or `^22.x` (LTS)

---

## Local Installation & Setup

Follow these structured steps to clone, configure, and execute the system inside your XAMPP local environment.

### 1. Clone the Repository

Move to your XAMPP development directory (usually `C:\xampp\htdocs`) or your preferred development folder, then run:

```bash
git clone https://github.com/yourusername/your-repository-name.git
cd your-repository-name
```

### 2. Dependency Orchestration

Install backend PHP packages and frontend asset dependencies concurrently:

```bash
# Install PHP dependencies via Composer
composer install

# Install Node modules for Tailwind CSS & Vite
npm install
```

### 3. Environment Configuration

Duplicate the local environment template and generate the application encryption key:

```bash
# Setup environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Open the newly created `.env` file and adjust the database credentials to map your XAMPP MySQL configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database Schema Alignment

Create a new empty database inside **phpMyAdmin** (e.g., named `your_database_name`), then execute migrations along with structural seeders:

```bash
php artisan migrate --seed
```

---

## Development Compilation & Server Execution

To initialize the application locally, you need to boot up both the backend application server and the reactive frontend asset pipeline.

### Step A: Start XAMPP Services

1. Open the **XAMPP Control Panel**.
2. Start the **Apache** and **MySQL** modules.

### Step B: Run Compilation Pipelines

Open two separate terminal panes within the root directory of the project:

**Terminal 1: Vite Asset Engine (Tailwind CSS Compilation)**

```bash
npm run dev
```

**Terminal 2: Laravel Application Engine**

```bash
php artisan serve
```

Once executed successfully, access the local system infrastructure at:  
**`http://127.0.0.1:8000`**

---
