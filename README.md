Prerequisites
-------------

-   A server with Docker and Docker Compose installed
-   A domain name pointing to the server's IP address

## Step 1: Clone the Project
-------------------------

Clone your project to the server using the following command:

`git clone <project-git-repo-url>`

## Step 2: Set up your .env file

Only two modifications are required for the .env file, apart from its regular requirements: 

1. The application uses the lcobucci/jwt package, and there are some variables you need to add to your env so things work, for example: 

`JWT_ALGORITHM=HS256`
`JWT_SECRET="hiG8DlOKvtih6AxlZn5XKImZ06yu8I3mkOzaJrEuW8yAv8Jnkw330uMt8AEqQ5LB"`
`JWT_EXPIRATION="+60 minutes"`
`JWT_TIMEZONE="Africa/Nairobi"`

2. You should also put down your mailer credentials so that the project can send emails

## Step 3: Build the Docker Images
-------------------------------

Navigate to the project directory and build the Docker images using the following command:

`docker-compose build`

## Step 4: Start the Docker Containers
-----------------------------------

Start the Docker containers using the following command:

`docker-compose up -d`

The `-d` flag runs the containers in detached mode.

## Step 5: Install Composer Dependencies
----------------------

So that all third party libraries can work in your environment, you can can install them in your docker environment with this command:

`docker-compose exec app composer install`


## Step 6: Generate your application key 
-----------------------------

Generate your Application key with this command:

`docker-compose exec app php artisan key:generate`

## Step 7: Verify the Deployment
-----------------------------

Visit your localhost in a web browser to verify that your Laravel project has been successfully deployed. Please note the Nginx configuration file is located in the `docker-compose/nginx`folder in the Laravel project. The project currently points to the `8000` port but you can update this if need be.


`http://localhost:8000`

## Step 7: Have questions?
-----------------------------

Feel free to reach out and ask me if you're stuck anywhere. I'd be happy to help.


`fredrickmakoffu@gmail.com`

