# Building and resetting
When you're ready, start your application by running:
`docker compose up --build`.

If you need to reset the database type:
`docker compose down -v`

# PHPMyAdmin
You can access PHPMyAdmin by going to http://localhost:8080
The username is: root
The password is: password

# Webshop
## Using SSL 
You can access the webshop by going to https://localhost:433

## Not using SSL
You can access the webshop by going to http://localhost:80

# Blockchain
The blockchain can be built by typing:

`go build ./cmd/cli`

This command will output a file "cli". This is the executeable used when managing the blockchain.

Before starting a node a wallet has to be created, the following creates a new wallet:

`./cli wallet create -walletname "webshop"`

Now you can start a node by typing:

`./cli server start -nodeport 3000 -apiport 8081 -walletname "webshop" -ismining=true`

This starts a thread running on port 8081. More commands can be found under the blockchain directory. Each command should be executed against the desired port number.

# Attacks
All attacks I've tried can be found under the attacks folder. Simply follow the instructions for each attack to replicate them. The attacks only work on the vulnerable version.