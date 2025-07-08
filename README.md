# Secure E-commerce Platform with Custom Blockchain

This project is a full-stack e-commerce web application that uses a custom-built blockchain for handling payments. It was developed to explore and demonstrate common web security principles.

The application is presented in two distinct versions, located in separate directories:
*   `eitf06-project-vulnerable`: An intentionally insecure version to demonstrate common web vulnerabilities like SQL Injection, Cross-Site Scripting (XSS), and Cross-Site Request Forgery (CSRF).
*   `eitf06-project-safe`: A hardened version that implements security best practices to mitigate the vulnerabilities found in the other version.

The backend is built with **PHP** and **MariaDB**, running in a **Docker** environment. The payment system is powered by a custom **Go-based blockchain**, which handles wallet creation, transactions, and mining.

## Features

-   **Full E-commerce Functionality**: User registration and login, product catalog, shopping cart, and a checkout system.
-   **Blockchain Payment System**:
    -   Custom blockchain built in Go.
    -   Users can create wallets and send a custom cryptocurrency ("SC").
    -   The webshop processes orders by verifying transaction IDs on the blockchain.
-   **Dockerized Environment**: The entire application stack (Apache, PHP, MariaDB, phpMyAdmin) is containerized with Docker Compose for easy setup and deployment.
-   **Security Demonstrations**: Includes clear examples and instructions for executing common web attacks on the vulnerable version and showcases the corresponding security fixes in the safe version.

## Technology Stack

-   **Backend**: PHP
-   **Database**: MariaDB
-   **Blockchain**: Go
-   **Web Server**: Apache
-   **Containerization**: Docker, Docker Compose

## Getting Started

### Prerequisites

-   [Docker](https://www.docker.com/products/docker-desktop/)
-   [Docker Compose](https://docs.docker.com/compose/install/)
-   [Go programming language](https://golang.org/dl/) (for interacting with the blockchain CLI)

### 1. Running the Web Application

The application is containerized, so you can easily run it with Docker Compose.

1.  **Choose a version**: Navigate into either the `eitf06-project-safe` or `eitf06-project-vulnerable` directory.
    ```bash
    # For the secure version
    cd eitf06-project-safe

    # OR for the vulnerable version
    cd eitf06-project-vulnerable
    ```
2.  **Build and start the containers**:
    ```bash
    docker compose up --build
    ```
3.  **Access the application**:
    -   **Webshop**: [https://localhost](https://localhost) (Uses a self-signed SSL certificate)
    -   **phpMyAdmin**: [http://localhost:8080](http://localhost:8080)
        -   **Username**: `root`
        -   **Password**: `password` (from `db/password.txt`)

4.  **Stopping and resetting the database**:
    To stop the application and completely remove the database volume, run:
    ```bash
    docker compose down -v
    ```

### 2. Running the Blockchain

The blockchain node and its command-line interface (CLI) run on your host machine.

1.  **Navigate to the blockchain directory**:
    ```bash
    # From the project root, go to either the safe or vulnerable blockchain folder
    cd eitf06-project-safe/blockchain/
    ```
2.  **Build the CLI**:
    ```bash
    go build ./cmd/cli
    ```
    This will create an executable file named `cli`.

3.  **Create Wallets**: You'll need at least two wallets: one for the webshop and one for a customer.
    ```bash
    # Create webshop wallet
    ./cli wallet create -walletname "webshop"

    # Create a customer wallet
    ./cli wallet create -walletname "customer"
    ```

4.  **Start the Blockchain Node**: Start a mining node using one of the created wallets (e.g., the webshop's). This node will listen for transactions and mine new blocks.
    ```bash
    # Start the node, which will also be the API server
    ./cli server start -nodeport 3000 -apiport 8081 -walletname "webshop" -ismining=true
    ```

### 3. Interacting with the Blockchain

You can use the CLI to interact with the running node's API.

-   **Get Wallet Address**:
    ```bash
    ./cli server getwalletaddress -apiport 8081
    ```
-   **Get Wallet Balance**:
    ```bash
    ./cli server getwalletbalance -apiport 8081
    ```
-   **Send a Transaction**:
    ```bash
    # Replace <TO_ADDRESS> with the recipient's address
    ./cli server sendtransaction --apiport 8081 --to "<TO_ADDRESS>" -amount 10
    ```

## Security Vulnerabilities & Mitigations

This project's primary goal is to demonstrate web security.

### Vulnerabilities (in `eitf06-project-vulnerable`)

The vulnerable version is susceptible to several common attacks. Step-by-step instructions for replicating them can be found in the `eitf06-project-vulnerable/attacks/` directory.
-   **SQL Injection**: Lack of prepared statements allows attackers to manipulate database queries.
-   **Cross-Site Scripting (XSS)**: Failure to sanitize user input allows attackers to inject malicious scripts that execute in other users' browsers.
-   **Cross-Site Request Forgery (CSRF)**: A combination of XSS and predictable form handling can allow an attacker to force a logged-in user to perform actions they did not intend, such as adding an item to their cart.
-   **Insecure Password Policies**: Weak password requirements and no blacklisting make accounts easy to compromise.

### Security Measures (in `eitf06-project-safe`)

The safe version implements the following countermeasures:

-   **Input Sanitization**: All user-provided data is sanitized using `htmlspecialchars()` before being rendered on the page, preventing XSS.
-   **Prepared Statements**: Database queries are executed using PDO prepared statements, which separates SQL logic from user data and prevents SQL Injection.
-   **Strong Password Policies**: The registration system enforces password complexity (length, character types) and checks against a blacklist of over 10,000 common passwords.
-   **Hardened PHP Configuration**: The `php.ini` file is configured to disable dangerous functions, hide PHP version information, and enforce secure session cookie settings (`HttpOnly`, `Secure`, `SameSite`).
-   **Login Throttling**: The system tracks failed login attempts to mitigate brute-force attacks.