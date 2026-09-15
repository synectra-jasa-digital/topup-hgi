# CodeIgniter 4 Framework

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds the distributable version of the framework.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Contributing

We welcome contributions from the community.

Please read the [*Contributing to CodeIgniter*](https://github.com/codeigniter4/CodeIgniter4/blob/develop/CONTRIBUTING.md) section in the development repository.

## Server Requirements

PHP version 8.2 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - The end of life date for PHP 8.1 was December 31, 2025.
> - If you are still using below PHP 8.2, you should upgrade immediately.
> - The end of life date for PHP 8.2 will be December 31, 2026.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library

## Production security configuration

Set these values in the production `.env` (do not commit secrets):

```dotenv
CI_ENVIRONMENT = production
app.baseURL = 'https://your-domain.example/'
app.allowedHostnames = 'your-domain.example,www.your-domain.example'
app.proxyIPs = '10.0.0.10'
security.CSPEnabled = true
database.default.encrypt = true
cache.handler = redis
cache.backupHandler = file
redis.host = 127.0.0.1
redis.port = 6379
redis.password = 'replace-with-secret'
```

Use `app.proxyIPs` only for trusted reverse-proxy addresses. Do not put API credentials in views or source code. Production requires HTTPS, a valid `encryption.key`, strict database mode, and a shared Redis cache when multiple application instances are running.

### Deployment checklist

- [ ] Set `CI_ENVIRONMENT=production`, HTTPS `app.baseURL`, hostname allowlist, dan proxy allowlist.
- [ ] Pastikan `encryption.key`, Wablas, database, dan Redis berasal dari secret manager/environment.
- [ ] Jalankan `php spark migrate --all` pada database target setelah memverifikasi backup dan migration history.
- [ ] Pastikan `php spark uploads:audit` berjalan dan folder upload menolak eksekusi PHP/script.
- [ ] Uji checkout, upload bukti pembayaran, verifikasi/penolakan admin, invoice dengan token salah, cek status, pengajuan bongkar, perubahan status, dan retry notifikasi.
- [ ] Uji backup create/download/delete dengan akun owner dan verifikasi retention.
- [ ] Jalankan `php vendor/bin/phpunit --no-coverage`, `composer validate --strict`, `composer audit --locked`, dan `npm run build:css`.
