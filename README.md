# MoneyMo

MoneyMo: Money Monitor is an innovative Modern Digital Receipt Keeper and Inventory Tracker. This project utilizes **Tailwind CSS** and **jQuery** for the frontend, and **Vanilla PHP** for the backend API.


## Table of Contents

- [MoneyMo](#moneymo)
  - [Table of Contents](#table-of-contents)
  - [Features](#features)
  - [Requirements](#requirements)
  - [Installation](#installation)
  - [Configuration](#configuration)
  - [Usage](#usage)
  - [API Documentation](#api-documentation)
  - [Folder Structure](#folder-structure)
  - [Contributing](#contributing)
  - [Contribution Guidelines](CONTRIBUTING.md)
  - [License](#license)


## Features

- [Add features specific here]
- [Add features specific here]
- [Add features specific here]


## Requirements

Before you begin, ensure you have the following installed:

- **XAMPP** - Includes Apache (web server) and PHP. Download and install it from [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html).
- **Node.js** (version X.X or higher) - Includes npm for managing frontend dependencies. Download and install it from [https://nodejs.org/](https://nodejs.org/).
- **A modern web browser** - For testing and using the application.


## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/uzzielkyle/MoneyMo.git .
   ```

2. **Set up XAMPP**:
   - Move the project folder to the `htdocs` directory inside your XAMPP installation folder (e.g., `C:\xampp\htdocs\`).
   - Start Apache from the XAMPP Control Panel.

3. **Install frontend dependencies**:
   Open a terminal in the project folder and run:
   ```bash
   npm install
   ```

4. **Build Tailwind CSS**:
   Run the following command to generate the CSS file:
   ```bash
   npm run build:tailwind
   ```

5. **Access the project**:
   Open your browser and navigate to:
   ```
   http://localhost/MoneyMo/public/
   ```


## Configuration

- **Environment Variables**: Create a `.env` file and populate it with the necessary values.


## Usage

1. Start your web server.
2. Open your browser and navigate to the project URL.
3. Use the frontend interface to interact with the PHP API.


## API Documentation

The PHP API provides the following endpoints:

- **GET /api/resource**: Fetch a list of resources.
- **POST /api/resource**: Create a new resource.
- **PUT /api/resource/{id}**: Update an existing resource.
- **DELETE /api/resource/{id}**: Delete a resource.

For more details, refer to the [API Documentation](docs/api/index.md).


## Folder Structure

```
MoneyMo/
├── src/                  # Frontend source files (HTML, CSS, JS)
│   └── css/              # Source CSS files for the project
│       └── input.css     # Tailwind CSS input configuration
├── public/               # Publicly accessible frontend assets (compiled files)
│   ├── assets/           # Images, fonts, and other static assets
│   ├── css/              # Compiled Tailwind CSS output (e.g., tailwind.css)
│   ├── js/               # Compiled and custom JavaScript files (including jQuery)
│   └── index.php         # Main entry point for the frontend (serves HTML)
├── api/                  # PHP backend files (API logic)
│   ├── index.php         # Main backend file (API entry point)
│   └── ...               # Additional backend PHP files
├── docs/                 # Documentation files
│   ├── api/              # API documentation
│   │   └── index.md      # API documentation landing page
│   └── interface/        # UI user guide documentation
│       └── index.md      # Interface user guide landing page
├── .gitignore            # Specifies files and directories to ignore in git
├── LICENSE               # Project license file
├── package.json          # NPM package dependencies and scripts
└── README.md             # Project README file       
```


## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/YourFeatureName`).
3. Commit your changes (`git commit -m 'Add some feature'`).
4. Push to the branch (`git push origin feature/YourFeatureName`).
5. Open a pull request.

Please follow these [guidelines](CONTRIBUTING.md) to ensure smooth collaboration and maintain high-quality documentation and code.

  

## License

This project is licensed under the [MIT License](LICENSE).
 
