A aplicação tem o objetivo de implementar uma api RestFull com os recursos contatos e empresas.

A API Rest é desenvolvida em `php 8`.

O sistema contem as seguintes entidades e seus respectivos campos:

- Usuário
    - Nome: obrigatório para preenchimento
    - E-mail: obrigatório para preenchimento
    - Telefone: não obrigatório
    - Data de nascimento: não obrigatório
    - Cidade onde nasceu: não obrigatório
    - Empresas: obrigatório para preenchimento

- Empresa
    - Nome: obrigatório para preenchimento
    - CNPJ: obrigatório para preenchimento
    - Endereço: obrigatório para preenchimento
    - Usuários: obrigatório para preenchimento

A regra de relacionamento para `Usuário` e `Empresa` é  __n para n__

### Banco

O banco de dados escolhido foi o MySQL. O banco de dados MySQL já tem se consolidado no mercado como um banco de dados robusto e seguro, sendo ideal para utilização em sistemas que lidam com dados sensiveis e integridade de dados. Além disso o MySQL possui o recurso de transactions que é essencial para atomicidade das operações realizadas no banco possibilitando maior garantia de consistência dos dados. O banco mysql possui quando habilitado na criação de suas tabelas ENGINE=InnoDB, o uso de chaves estrangeiras, essencial para trabalhar com bancos que possuem diferentes entidades relacionadas.

## Tutorial de como rodar a aplicação:

### Softwares necessários

- Docker
- Docker-Compose

### Paso a passo

- Instalando

Se possui o git instalado:

Clone o repositório em: https://github.com/MatheusHonorato/php-projeto-pessoal

Se não possui o git instalado:

Acesse:  https://github.com/MatheusHonorato/php-projeto-pessoal

Clique em: CODE > Download ZIP

- Rodando a api

Após efetuar o download do projeto é necessário executar os seguintes passos:

- Habilite a instalação do seu docker
- Acesse a raiz do projeto e rode: 'docker run build' para fazer o build do arquivo Dockerfile
- copie o arquivo '.env-example' e renomeie para '.env'
- Após o build rode o comando: 'docker-compose up -d' para subir os containers, rodar a aplicação e o script build para criar as tabelas no banco e inserir dados default.
- Acesse o bash do container php com o comando 'docker exec -ti app bash' e rode o comando 'composer install' para instalar as dependencias do projeto.
- Aguarde alguns segundos e acesse o servidor da aplicação que estará disponível em: 'http://localhost:8000'
- Se ocorrer algum erro rode 'docker-compose ps' e verifique a coluna 'State' de cada container, se alguma não estiver como 'Up' provavelmente alguma porta já está sendo utilizada no sistema,
para resolver de forma rapida e conseguir testar a aplicação altere as portas utilizadas pelos containers no arquivo docker-compose.yml, rode 'docker-compose down' e inicie o processo novamente.
- A API pode ser testada de maneira isolada em softwares como o insomnia ou postman.

- Rodando testes

Para rodar os testes é necessário acessar o container docker onde o php está sendo interpretado utilizando o seguinte comando: 'docker exec -ti app bash'. Em seguinda execute o comando 'vendor/bin/phpunit tests/' para rodar os testes.

Para verificar a cobertura dos testes passe a flag '--coverage-text'

# Rotas API:

## Companies

CompanyFind

    Método: GET

    Endereço: http://localhost:8000/companies/1

CompanyList

    Método: GET

    Endereço: http://localhost:8000/companies

CompanySearch

    Método: GET

    Endereço: http://localhost:8000/companies?name=empresa

CompanyCreate

    Método: POST

    Endereço: http://localhost:8000/companies

    JSON:

    {
        "name": "Empresa teste updaterrr",
        "cnpj": "12345600001",
        "address": "Rua exemplo",
        "user_ids": [1]
    }

CompanyUpdate

    Método: PUT

    Endereço: http://localhost:8000/companies/1

    JSON:

    {
        "name": "Empresa teste updaterrr",
        "cnpj": "12345600001",
        "address": "Rua exemplo",
        "user_ids": [1]
    }

CompanyDelete

    Método: DELETE

    Endeeço: http://localhost:8000/companies/1

## Users

UserFind

    Método: GET

    Endereço: http://localhost:8000/users/1

UserList

    Método: GET

    Endereço: http://localhost:8000/users

UserSearch

    Método: GET

    Endereço: http://localhost:8000/users?name=empresa

UserCreate

    Método: POST

    Endereço: http://localhost:8000/users

    JSON:

    {
        "name": "testek",
        "email": "testek@testekt.com",
        "date": "2020-05-05",
        "city": "moc",
        "phone": "3222222",
        "company_ids": [1]
    }

UserUpdate

    Método: PUT

    Endereço: http://localhost:8000/users/1

    JSON:

    {
        "name": "testek",
        "email": "testek@testekt.com",
        "date": "2020-05-05",
        "city": "moc",
        "phone": "3222222",
        "company_ids": [1]
    }

UserDelete

    Método: DELETE

    Endeeço: http://localhost:8000/users/1


## Um pouco sobre a aplicação (API)

Stack utilizada:

- Git
- Docker
- PHP 8.1.0
- Composer 2
- Mysql 5.7
- phpmyadmin
- nginx

Pacotes:

- vlucas/phpdotenv 5.5
- phpunit/phpunit 10.0

Descrição

A aplicação foi desenvolvida utilizando php orientado a objetos com tipagem forte e arquitetura model, controller. Além dos models e controllers para um maior desacoplamento da aplicação foi aplicado a variação do padrão singleton chamada monostate (variação que não quebra os principios SOLID), na conexão com o banco de dados para garantir que não sejam abertas varias conexões desnecessarias com o banco.
Uma versão simplificada do padrão querybuilder foi utilizada para abstrair as querys do banco de dados e repositories foram criados para que os controllers não ficassem inchados com regras de negócio. O sistema de roteamento da api é feito carregando os controladores a partir dos endereços das rotas com o respectivo método http ex: rota 'users' utilizando o método http 'get' carrega o  método de nome 'get' no controlador 'UsersController'.

## Pricipais dificuldades e duvidas

A principal dificuldade durante o processo de desenvolvimento foi trabalhar com uma abstração para o banco de dados. No desenvolvimento da aplicação tive a ideia de utilizar o query buildar para abstrair as consultas do banco e deixar o software mais desacoplado, o que acabou levando um bom tempo de desenvolvimento e na minha opinião um certo overenginner.

## Melhorias propostas

- Adicionar diagrama das das classes
- Adicionar tabelas dos bancos
- Rodar PHPCS
- Refatoração do query builder
- Refatoração do sistema de rotas
- Implementação de testes