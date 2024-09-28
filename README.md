<p align="center">
    <a href="https://ibb.co/KNbHJgw">
        <img src="https://i.ibb.co/7gjFqD4/suru-light.png" width="200" alt="Suru Logo" border="0">
    </a>
</p>

# Suru - Backend
Development of the backend infrastructure for the Suru platform to streamline the purchase, sale, and rental of properties.

# Instructions
### 1: Clone repository  
```bash
git clone https://github.com/Agile-Innovators/suru-backend.git
```

### 2: Install dependencies  
```bash
npm install
```

### 3: Create .env file  
```bash
cp .env.example .env
```

### 4: Generate application keys  
```bash
npm generate-keys
```

### 5: Configure the .env file  
Edit the .env file to configure the database connection and other necessary settings:

- **Database**: Set your database name
  ```bash
  DB_DATABASE=suru-backend
  ```

- **Resend configuration**: Set your Resend API key
  ```bash
  RESEND_API_KEY=your_resend_api_key_here
  ```

- **Email configuration**: Set the mailer settings
  ```bash
  MAIL_MAILER=resend
  MAIL_FROM_ADDRESS="onboarding@resend.dev"
  MAIL_FROM_NAME=Suru
  ```

### 6: Execute Database's migrations  
```bash
php artisan migrate
```

### 7: Load Seeders information  
```bash
php artisan db:seed
```

### 8: Start the project  
```bash
npm run dev
```

### Contributors (Fullname/github users)
* Ashley Rojas Pérez, @allyprz
* Kevin Guido Urbina, @kevGuido22