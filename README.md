<p align="center">
    <a href="https://ibb.co/KNbHJgw">
        <img src="https://i.ibb.co/7gjFqD4/suru-light.png" width="200" alt="Suru Logo" border="0">
    </a>
</p>

# Suru - Backend
Development of the backend infrastructure for the Suru platform to streamline the purchase, sale, and rental of properties.

# Instructions
### 1: Clone repository
git clone https://github.com/Agile-Innovators/suru-backend.git
cd suru-backend

### 2: Install dependencies
npm install

### 3: Create .env file
cp .env.example .env.

### 3: Generate application keys
npm generate-keys

### 4: Configure the .env file
Edit the .env file to configure the database connection. Make sure that the database details are correct and match the project name:
DB_DATABASE=suru-backend

### 5: Execute Database's migrations
php artisan migrate

### 6: Load Seeders information
npm run seeders

### 7: Encrypt user passwords
php artisan migrate:refresh --step=1 --path=database/migrations/2024_06_16_234743_encrypt_existing_passwords.php

### 8: Start the project
npm run dev

### Contributors (Fullname/github users)
* Ashley Rojas Pérez, @allyprz
* Kevin Guido Urbina, @kevGuido22