# PHP AI From Scratch

![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue)
![Status](https://img.shields.io/badge/status-educational-green)
![License](https://img.shields.io/badge/license-open--source-lightgrey)

This project is an educational experiment focused on **building Artificial Intelligence and Machine Learning algorithms from scratch using pure PHP**, without relying on external AI/ML frameworks.

The goal is to deeply understand the mathematical and algorithmic foundations behind AI models by implementing them manually.

---

## ✨ Features

* Pure PHP implementations (no ML frameworks)
* Simple and Multiple Linear Regression
* Matrix inversion using Gauss–Jordan elimination
* Basic AI model interface for extensibility
* CSV-based datasets for training and testing
* Composer-based autoloading

---

## 🧠 Implemented Algorithms

### 1. Simple Linear Regression

* Uses the analytical solution based on least squares
* Computes slope and intercept manually
* Suitable for datasets with a single independent variable

### 2. Multiple Linear Regression

* Uses matrix operations
* Coefficients calculated via the Normal Equation:

```
θ = (XᵀX)⁻¹ Xᵀy
```

* Matrix inversion is done using **Gauss–Jordan elimination**, implemented from scratch

### 3. Gauss–Jordan Matrix Inversion

* Supports square matrices
* No external math libraries
* Core foundation for multiple regression

---

## ▶️ How to Run

### Requirements

* PHP 8.1+
* Composer
* Linux / WSL / macOS / Windows

### Install dependencies

```
composer install
```

### Run Simple Linear Regression

```
php run_simple.php
```

### Run Multiple Linear Regression

```
php run_multiple.php
```

### Run All

```
php index.php
```
---

## 📊 Datasets

* `simple.csv` → used for simple linear regression
* `multiple.csv` → used for multiple linear regression

CSV files should follow a numeric, comma-separated format.

---

## 🎯 Educational Purpose

This project is **not intended for production use**.

It is designed to:

* Learn AI and Machine Learning fundamentals
* Understand linear algebra applied to ML
* Practice algorithmic thinking in PHP
* Explore how ML works internally, step by step

---

## 📜 License

This project is open-source and available for educational use.

---

## 👤 Author

Developed by **Leoncio**

Feel free to fork, study, and experiment!