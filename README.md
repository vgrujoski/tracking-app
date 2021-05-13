# tracking-app

Steps to get the repository:

1. Pull the project using `git clone git@github.com:vgrujoski/tracking-app.git`.
2. Go to project's root directory and run `composer install`.
3. Go to `/bootstrap` directory and run the following command `cp .env.local .env`.
4. Configure your `.env` file with your db, email, localhost port number, and set your `GENERATOR_TOKEN_SECRRET_KEY` credentials.
5. Go to the root directory again and open the `phinx.php` file to set your db credentials in the `development` section.
6. Run the following command `php vendor/bin/phinx migrate
` in the terminal from the root directory.
7. Go to `/public` directory and run `php -S localhost:<PORT_NUMBER>`.
8. Open some API Client app(eg. POSTMAN) and start testing the application.


Steps to test:
1. Sign Up.
2. Copy the token from the response.
3. Open `/pixel` route - Select Bearer Token Authorization and paste the token, no need to put body parameters, the body parameters are filled in the backend.
4. After signed up you will recieve email to verify the registration.
5. Copy the Activation code from the email.
6. Go to `/auth/confirm` route and paste the code as a body parameter.
7. Open `/pixel` route again and execute.
8. Check database data.
9. After successful account verification you can log in and you will get another token.
