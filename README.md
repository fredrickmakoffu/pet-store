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

Set up your RapidAPI credentials here, for example: 

`RAPIDAPI_KEY=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`
`RAPIDAPI_HOST=<your-host-file-here>`

You should also put down your mailer credentials so that the project can send emails

## Step 2: Build the Docker Images
-------------------------------

Navigate to the project directory and build the Docker images using the following command:

`docker-compose build`

## Step 3: Start the Docker Containers
-----------------------------------

Start the Docker containers using the following command:

`docker-compose up -d`

The `-d` flag runs the containers in detached mode.

## Step 4: Set Up Laravel
----------------------

Enter the Laravel container using the following command:
com

`docker exec -it <container-name> /bin/bash`

Replace `<container-name>` with the name of your Laravel container.

Navigate to the project directory and install the dependencies using the following commands:

`composer install`

`npm install`

Run the Laravel Mix build using the following command:

`npm run prod`


## Step 7: Verify the Deployment
-----------------------------

Visit your domain name in a web browser to verify that your Laravel project has been successfully deployed. Please note the Nginx configuration file is located in the `docker-compose/nginx`folder in the Laravel project. The project currently points to the `8080` port but you can update this if need be.


`http://localhost:8080`

