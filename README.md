# AirSnG: Peer-to-Peer Luggage Storage for Students
https://wangyajie.xo.je/
**A storage matching platform built for the Singapore community**

## The Problem & Motivation

International students at NTU (Singapore) often face high costs and logistical hurdles when clearing dorms during vacations. Commercial storage is built for bulk, not for a student with one or two suitcases.

Airsng provides a cheaper alternative to commercial warehouses, at the same time enabling nearby residents to generate micro-revenue.

## Tech Stack

- Backend: PHP utilizing PDO to prevent SQL injection.

- Database: MySQL with a focus on Relational Integrity and Indexing for optimized query performance.

- API Integration: OneMap REST API (SLA Singapore) for geocoding 6-digit postal codes into precise building names and coordinates.

- Mathematics: Implementation of the Haversine Formula to calculate great-circle distances between students and hosts.

- Frontend(AI-powered): Bootstrap 5.3 for a responsive, mobile-first user interface designed for students on the move.

## Features & Functionality

### For Students

* **Post Requests:** Specify start and end date, luggage count, location, and budget.
* **Smart Matching:** View a ranked list of hosts based on a multi-tier algorithm (Time, Price, Location, Service).
* **Management:** Track request status (Pending, Active, Matched).

### For Hosts
* **List Space:** Define availability windows, pricing, and maximum capacity.
* **Service Tiers:** Offer value-adds like "help-moving" or "self-pickup."

---

##  System Architecture

### The Matching Algorithm

The core of AirSnG is the matching logic in `match.php`, which ranks hosts using a tiered priority system:
Priority,Factor,Implementation Detail
Tier S,Time & Capacity,Soft-penalty overlap calculation and size compatibility.
Tier A,Proximity,Haversine Formula distance calculation via OneMap API coordinates.
Tier B,Price,Normalized budget matching.
Bonus,Service Level,"3-tier service weights (Standard, Assistance, Full-Service)."

## Project Structure

* `index.php` - Entry point & Authentication gate.
* `student.php` / `host.php` - Role-specific dashboards.
* `add.php` / `offer.php` - Form handling for new listings with input validation.
* `pdo.php` - Centralized database connection logic & helper functions.

## Roadmap & Future Improvements
**Social Features:** In-app chat system and host/student rating system.
