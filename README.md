# AirSnG: Peer-to-Peer Luggage Storage for Students
https://wangyajie.xo.je/
AirSnG is a web-based matching platform designed to connect international students needing temporary luggage storage with local residents who have spare space. Think of it as "Airbnb for suitcases."

## 🚀 The Problem & Motivation

International students at NTU (Singapore) often face high costs and logistical hurdles when clearing dorms during vacations. Commercial storage is built for bulk, not for a student with one or two suitcases.

* **Affordability:** Provides a cheaper alternative to commercial warehouses.
* **Convenience:** Utilizes nearby residential vacancies.
* **Community:** Enables local residents to generate micro-revenue.

## 🛠️ Tech Stack

Backend: PHP 8.x (using PDO for secure, injected-protected database interactions).
Database: MySQL.
Frontend: Bootstrap 5 (Responsive UI), HTML5, CSS3.
Security: PHP password_hash and password_verify for industry-standard credential protection.

## 📋 Features & Functionality

### For Students

* **Post Requests:** Specify luggage count, size, and budget.
* **Smart Matching:** View a ranked list of hosts based on a multi-tier algorithm (Size, Time, Price, Location).
* **Management:** Track request status (Pending, Active, Matched).

### For Hosts

* **List Space:** Define availability windows, pricing, and maximum capacity.
* **Service Tiers:** Offer value-adds like "help-moving" or "self-pickup."

---

## 🏗️ System Architecture

### The Matching Algorithm

The core of AirSnG is the matching logic in `match.php`, which ranks hosts using a tiered priority system:
Weight	Factor	Logic
50%	Time Overlap	Verified dates: Host availability must fully encompass the student's request.
30%	Capacity	Logical check: Does the host have the physical volume and bag-count limit?
15%	Price	Competitive ranking: Hosts under budget receive higher visibility.
5%	Service	Bonus points for hosts offering pickup/delivery services.

### Database Schema

The system relies on a relational MySQL database. Below is the entity relationship overview:
erDiagram
    USERS ||--o{ REQUESTS : "places"
    USERS ||--o{ OFFERINGS : "provides"

    USERS {
        int user_id PK
        string email
        string password
        string phone
        enum user_type
    }

    REQUESTS {
        int request_id PK
        int user_id FK
        int luggage_amount
        string total_size
        date drop_date
        date leave_date
        decimal max_price
    }

    OFFERINGS {
        int offer_id PK
        int user_id FK
        date available_from
        date available_to
        int max_num
        decimal charges
    }

## 📂 Project Structure

* `index.php` - Entry point & Authentication gate.
* `student.php` / `host.php` - Role-specific dashboards.
* `add.php` / `offer.php` - Form handling for new listings with input validation.
* `pdo.php` - Centralized database connection logic.

## 📈 Roadmap & Future Improvements

* [ ] **Granular Inputs:** Allow piece-by-piece luggage descriptions.
* [ ] **Geolocation:** Integrate Google Maps API for distance-based sorting rather than rigid area names.
* [ ] **Social Features:** In-app chat system and host/student rating system.
* [ ] **Expansion:** Scalability beyond the Jurong West/NTU region to all of Singapore.

## 🛠️ Installation & Setup

1. Clone the repository.
2. Import the SQL schema provided in `database.sql` into your MySQL server.
3. Configure your credentials in `pdo.php`.
4. Run via MAMP or any PHP-enabled web server.
