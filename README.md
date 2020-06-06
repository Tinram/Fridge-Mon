
# Fridge Mon

### Monitor fridge food expiry dates.


[1]: https://tinram.github.io/images/fridge-mon.png
![fridge-mon][1]


## Purpose

Food gets buried in my fridge and it's usually found only after the expiry date.

Fridge Mon is a simple web utility to help me prevent food rot and wastage.


## Requirements

+ PHP server with version 7.2+ and sqlite3 module enabled.


## Setup

Clone the repository (or extract the ZIP archive) into the server's web directory
e.g.

```bash
    cd /var/www/html

    sudo git clone https://github.com/Tinram/Fridge-Mon.git

    sudo chown -R <username>:www-data Fridge-Mon/
```

(Debian-based; use `apache` instead of `www-data` for Red Hat-based distros)

```bash
    sudo chown www-data Fridge-Mon/log/badlog.txt

    sudo chmod 600 Fridge-Mon/log/badlog.txt
```

SQLite file operation (requires actioning on the directory itself):

```bash
    sudo chown -R www-data Fridge-Mon/db/
```

Check the configuration file constants: *config/config.php*

Change the users and the user password hashes (`CONFIG_USER1`, `CONFIG_USER1_PASS`).  
The default users are *martin* and *alison*, and both passwords are *P@55w0rd*.  
(More users can be added here, and editing the relevant locations in *classes/login.class.php*.)

Passwords are stored as SHA-256 hashes. `CONFIG_USER1_PASS` etc should be replaced with a hash generated from either a website service or by running one of the following commands in a terminal and copying the output hash:

*Bash*

```bash
    echo -n 'PASSWORD' | sha256sum
```

*PHP*

```bash
    php -r "echo hash('sha256', 'PASSWORD');"
```

*Python*

```python
    python -c "import hashlib;print(hashlib.sha256('PASSWORD'.encode()).hexdigest())"

    python3 -c "import hashlib;print(hashlib.sha256('PASSWORD'.encode()).hexdigest())"
```

### Manually Create the SQLite Database

Fridge Mon includes an initial SQLite database: *db/fridgemon.sqlite3*

Database file manual set-up instructions are in *sql/fridgemon.sql*


## Viewing and Searching

*http://localhost/Fridge-Mon*  
*http://<your_server>/Fridge-Mon*


## Other Files

The SQLite database schema is available at *sql/fridgemon.sql*

Unsuccessful log-in attempts are recorded in *log/badlog.txt*


## Credits

+ Angel Marin and Paul Johnston: JavaScript SHA-256 hash function.
+ webtoolkit.info
+ Tigra Calendar.


## License

Fridge Mon is released under the [GPL v.3](https://www.gnu.org/licenses/gpl-3.0.html).
