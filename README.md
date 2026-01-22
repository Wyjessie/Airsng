# Airsng
# High-level Description
1. Motivation: 
    As an international student living on campus at NTU, my friends and I are all worried about where to keep our luggages during vacations when we have to clear our dorm rooms. 
    
    The current popular option is to use commercial storage places. However, these are relatively expensive and aim for users with lots of stuff. Whereas we just need a place to maybe put one or two suitcases.
    
    We also note that nearby residents might be willing to help out and at the same time generate some revenue with their vacancies at home.

    Therefore, we just need a matching platform to benefit both sides. I named it Airsng beacause:
        1. It is similar to Airbnb, only in this case the renter is luggage
        2. For now we set the context in SG, short for Singapore. That's where SnG comes from

2. Implementation
    Users first create an account that takes in their email, password, phone number, and type(student/host)

    Log in.

    View past requests/offerings. If no active ones, they may add a new one

    Students can then navigate to the matching page to view suitable hosts ordered based on size, price, time, location compatibility.

3. Areas to improve
    1. More flexibility and details in inputting the luggage's information: can input piece by piece
    2. More flexibility in location selecting. Maybe don't need rigidly define location, but use something to calculate the distance
    3. Also include a system to support chatting within the platform and also a rating system

4. Looking ahead
    - Currently: in the NTU region(only support inputting nearby locations like JP, Boonlay, etc.), between students and nearby residents
    - Future:
        - Expand the area: to the entire Singapore, Asia, or even globally
        - Could involve tourists who just need a place to put their luggages for a day or so
    - Market:
        - I believe is big, because as overseas studying becomes more and more popular, international students are going to keep increasing in number
        - Additionally, I often want somewhere nearby to put my stuff when on a trip





# PHP files functionality description:
## index.php
if you are not signed in:
    - choose student/host
    - sign in with email + password -> student.php or host.php
    - If no account yet -> signup.php

## student.php
1. Displays your request if any
    - if want to update status -> delete.php
2. If no active request yet, shows you access to add.php

## host.php
1. Displays your offering if any
    - if want to update status -> delete_h.php
2. If no active offerings yet, shows you access to offer.php, which allow you to add an offer

## match.php
1. Containes the algorithm to decide the best match, here are the main factors that adds to suitability:
    1. Tier S:
        - size compatibility
        - time overlap degree
    2. Tier A:
        - price
        - location
    3. Bonus:
        - Service level

2. Shows user the recommended list of hosts 
    - their offer information and their phone + email to allow contact

## delete.php, delete_h.php
    Option A: update status to 'matched' for students or 'booked' for hosts
    Option B: delete

## add.php, offer.php
    - Allows students or hosts to add a new request or offer only if they have no current active ones
    - Checks for input validity, e.g. no empty fields, certain fields must be numeric

## Miscellaneous files
pdo.php
    - connects to the database
logout.php
    - destroy session




# databases:
-- Create and use database
CREATE DATABASE IF NOT EXISTS luggage_storage CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE luggage_storage;

-- Users table (enhanced)
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    user_type ENUM('student', 'host') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Requests table (students)
CREATE TABLE requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    luggage_amount INT NOT NULL COMMENT 'Number of bags',
    total_size VARCHAR(50) NOT NULL COMMENT 'e.g., small/medium/large',
    drop_date DATE NOT NULL,
    leave_date DATE NOT NULL,
    max_price DECIMAL(10,2) NOT NULL,
    acceptable_areas VARCHAR(255) COMMENT 'e.g., NTU,Clementi,Boon Lay',  -- Added for better matching
    status ENUM('pending', 'active', 'matched', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Offerings table (hosts)
CREATE TABLE offerings (
    offer_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    available_from DATE NOT NULL,
    available_to DATE NOT NULL,
    max_num INT NOT NULL COMMENT 'Max luggage pieces',
    max_size VARCHAR(50) NOT NULL,
    charges DECIMAL(10,2) NOT NULL COMMENT 'Price per day per bag',
    location VARCHAR(100) NOT NULL,
    services VARCHAR(255) COMMENT 'e.g., help-moving,self-pickup',  -- Added for needs matching
    status ENUM('pending', 'active', 'booked', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Indexes for matching performance
CREATE INDEX idx_requests_user_status_dates ON requests(user_id, status, drop_date, leave_date);
CREATE INDEX idx_requests_areas ON requests(acceptable_areas);
CREATE INDEX idx_offerings_user_status_dates ON offerings(user_id, status, available_from, available_to);
CREATE INDEX idx_offerings_location ON offerings(location);

