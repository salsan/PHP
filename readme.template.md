# {{TITLE}}

<p align="center">
  <a href="https://github.com/{{VENDOR}}/{{NAME}}">
    <img src="https://github.com/salsan/PHP/assets/111319/6d94bc4a-14d4-46b0-bde5-9c0ccc690c3b" alt="{{TITLE}} Logo" />
  </a>
</p>

{{DESCRIPTION}}

![PHP](https://img.shields.io/badge/PHP-{{PHP_VERSION}}%2B-blue)
![License](https://img.shields.io/badge/License-{{LICENSE_BADGE}}-green)
![Composer](https://img.shields.io/badge/Composer-Latest-orange)

## Features

- **[Composer](https://getcomposer.org/)** for dependency management
- **[PHPUnit](https://phpunit.de/)** for unit testing
- **[PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer)** for enforcing coding standards
- **[PHPStan](https://phpstan.org/)** for static analysis
- Fully **[PSR-12](https://www.php-fig.org/psr/psr-12/)** compliant
- Suggested **[VSCode extensions](https://raw.githubusercontent.com/{{VENDOR}}/{{NAME}}/main/.vscode/extensions.json)** for an optimized development environment

## Requirements

- [**PHP**](https://www.php.net/downloads.php) {{PHP_VERSION}} or higher
- [**Composer**](https://getcomposer.org/) (latest version)
- [**Git**](https://git-scm.com/) for cloning the repository
- Optional: **[VSCode](https://code.visualstudio.com/?wt.mc_id=vscom_downloads)** with suggested extensions

## Getting Started

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/{{VENDOR}}/{{NAME}}.git
   cd {{NAME}}
   ```
2. Install dependencies via Composer:
   ```bash
   composer install
   ```

3. Run tests to ensure everything is set up correctly:
   ```bash
   ./vendor/bin/phpunit
   ```

### Usage

1. Start writing your PHP code in the `src/` directory.
2. Add your tests in the `tests/`` directory.
3. Run the following commands to ensure code quality:
   ```bash
   # Run PHPUnit tests
   ./vendor/bin/phpunit

   # Run PHP Code Sniffer
   ./vendor/bin/phpcs

   # Run PHPStan
   ./vendor/bin/phpstan analyse src tests
   ```

## Project Structure

```
.
├── src/                 # Your source code
├── tests/               # Test files
├── vendor/              # Composer dependencies
├── .gitignore
├── .phpcs.xml           # PHP Code Sniffer configuration
├── composer.json
├── phpstan.neon         # PHPStan configuration
├── phpunit.xml          # PHPUnit configuration
└── README.md
```

## Contributing

Contributions are welcome! To contribute:

1. Fork the repository.
2. Create a new branch for your feature or bugfix: `git checkout -b feature/your-feature-name`
3. Make your changes and commit them: `git commit -m 'Add some feature'`
4. Push to the branch: `git push origin feature/your-feature-name`
5. Submit a pull request with a clear description of your changes.

Please make sure to follow **PSR-12** coding standards and add tests for new features.

## Bug Reports

If you encounter any issues or bugs, please [open an issue](https://github.com/{{VENDOR}}/{{NAME}}/issues) on the GitHub repository.

---

## Author

<img src="https://github.com/{{VENDOR}}.png?size=140" width="120" height="120" style="border-radius:50%" />

**{{AUTHOR_NAME}}**
- 📧 <{{AUTHOR_EMAIL}}>

---

## License

© {{YEAR}} **{{AUTHOR_NAME}}** — <{{AUTHOR_EMAIL}}>
This project is licensed under the
**[{{LICENSE}} License]({{LICENSE_URL}})**.
