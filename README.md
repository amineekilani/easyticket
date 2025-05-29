# 🎫 EasyTicket

**EasyTicket** is a modern web platform designed to digitalize the process of football ticket sales in Tunisia. Built with **Symfony** for the backend and **Tailwind CSS** for the frontend, this application enables supporters to browse matches, select seats, and purchase tickets online in a secure and intuitive way.

## 🚀 Features

- 🏟️ Browse and filter upcoming football matches
- 🪑 Interactive stadium layout with seat selection
- 🛒 Smart shopping cart system for ticket management
- 💳 Secure online payment integration (Stripe)
- 📩 Digital ticket generation with QR code and email confirmation
- 🔐 Authentication and user profile management
- 🧑‍💼 Admin dashboard for managing users, teams, matches, stadiums, and sales
- 📊 Real-time sales statistics and stadium occupancy monitoring

## 🛠️ Tech Stack

- **Backend:** Symfony 5
- **Frontend:** Tailwind CSS, Twig
- **Database:** MySQL
- **Payment API:** Stripe
- **Version Control:** Git & GitHub

## 👥 Authors

- [Amine Kilani](https://github.com/amineekilani) 
- [Baha Eddine Manai](https://github.com/BahaManai) 

This project was developed as part of the Integration Project at ISET Radès.

- **Instructor**: Mrs. [Nesrine Elleuch](https://www.linkedin.com/in/nesrineelleuchcro) 
- **Academic Year**: 2024–2025

## 📦 Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/amineekilani/easyticket
   cd easyticket
   ```

2. Install dependencies:

```bash
composer install
```

3. Configure `.env` for DB, email & Stripe keys.

4. Run migrations and seed data:

```bash
php bin/console doctrine:migrations:migrate
```

5. Start local server:

```bash
symfony serve -d
```

## 📜 License

This project is for educational purposes and is not licensed for commercial use.

## 🤝 Contributing

Pull requests and feedback are welcome!  
Fork the repo, create a feature branch, and submit a pull request.

## 📬 Contact

For inquiries, you can reach us at [aminekilani@rades.r-iset.tn](mailto:aminekilani@rades.r-iset.tn) or [bahaeddinmanai7@gmail.com](mailto:bahaeddinmanai7@gmail.com).