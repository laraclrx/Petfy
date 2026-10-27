# 🐾 Petfy – Toda Patinha merece um lar!

Sistema web completo de **Adoção Responsável de Animais**, desenvolvido em **PHP 8.2+**, **MySQL / MariaDB** e **Vanilla HTML5, CSS3 & JavaScript**, estruturado e organizado de forma modular em pastas dedicadas para **Front-End**, **Back-End** e **Banco de Dados**.

---

## 📁 Estrutura de Pastas do Projeto

O projeto foi organizado com separação de responsabilidades:

```text
PETFY/
│
├── 📂 back-end/                     # Regras de negócio, autenticação, CRUDs e conexão
│   ├── 📂 config/
│   │   └── database.php             # Conexão PDO Singleton e helpers de sessão/flash
│   ├── 📂 auth/
│   │   ├── processa_login.php       # Autenticação segura com password_verify()
│   │   ├── processa_cadastro.php    # Registro de usuários com password_hash() Bcrypt
│   │   ├── verifica_auth.php        # Middleware de proteção de rotas e sessão
│   │   └── logout.php               # Encerramento seguro de sessão
│   ├── 📂 pets/
│   │   ├── processa_cadastro_pet.php # Cadastro de animal com upload seguro de foto
│   │   ├── processa_edicao_pet.php   # Atualização de dados do pet
│   │   └── processa_exclusao_pet.php # Exclusão com limpeza de imagens do disco
│   └── 📂 adocao/
│       ├── processa_solicitacao.php  # Envio de pedidos de adoção
│       └── processa_status_adocao.php# Atualização de status e finalização de adoção
│
├── 📂 front-end/                    # Interface do usuário, páginas, estilos e scripts
│   ├── index.php                    # Página inicial (Hero com nuvens, destaques e sobre)
│   ├── login.php                    # Tela de Login dividida (50/50 - fiel ao print)
│   ├── cadastro.php                 # Tela de Cadastro de Usuário (fiel ao print)
│   ├── logout.php                   # Ponto de saída do front-end
│   ├── 📂 pages/
│   │   ├── pets.php                 # Catálogo com filtros de espécie, raça, sexo e porte
│   │   ├── pet-detalhes.php         # Perfil do animal, saúde, histórico e modal de adoção
│   │   ├── cadastrar-pet.php        # Formulário multistep em 3 etapas com ViaCEP
│   │   ├── meus-pets.php            # Painel do tutor (pets cadastrados e pedidos)
│   │   ├── minhas-adocoes.php       # Painel do adotante e integração WhatsApp
│   │   └── perfil.php               # Edição de perfil do usuário e endereço
│   ├── 📂 includes/
│   │   ├── header.php               # Barra de navegação flutuante responsiva
│   │   ├── footer.php               # Rodapé padrão com links e redes sociais
│   │   └── flash_messages.php       # Mensagens de alerta (sucesso, erro, info)
│   ├── 📂 css/
│   │   ├── style.css                # Design system global (cores menta, teal, sombras)
│   │   ├── auth.css                 # Estilos específicos de login e cadastro
│   │   ├── pet-form.css             # Estilos do formulário multistep
│   │   └── pets-grid.css            # Estilos de cards de pets, catálogo e detalhes
│   ├── 📂 js/
│   │   ├── main.js                  # Controle de menus e alertas
│   │   ├── cep.js                   # Integração com a API ViaCEP
│   │   ├── pet-form.js              # Controle de steps e upload com preview
│   │   └── auth-validate.js         # Máscaras de telefone e validações
│   └── 📂 uploads/
│       ├── .htaccess                # Proteção contra execução de scripts maliciosos
│       └── 📂 pets/                 # Diretório de armazenamento das fotos dos pets
│
├── 📂 banco de dados/               # Scripts SQL e diagramas do banco de dados
│   ├── petfy_database.sql           # Script SQL completo com tabelas relacionais e dados
│   └── vidapet.sql                  # Script SQL espelhado
│
├── index.php                        # Ponto de entrada que redireciona para front-end/index.php
└── README.md                        # Documentação do projeto
```

---

## 🚀 Como Executar o Projeto

Você pode executar o Petfy de **duas formas**:

### Opção A: Execução Instantânea (Sem precisar configurar MySQL)
O Petfy possui suporte inteligente a banco de dados com **fallback automático em SQLite** embutido. Basta abrir o terminal na pasta do projeto e rodar:
```bash
php -S localhost:8000
```
Depois abra o navegador em: [http://localhost:8000](http://localhost:8000)

---

### Opção B: Execução via XAMPP (Apache + MySQL)

1. **Copiar o projeto para o Apache**:
   - Copie a pasta `PETFY` para `C:\xampp\htdocs\PETFY`

2. **Iniciar os Serviços**:
   - Abra o **XAMPP Control Panel** e inicie os módulos **Apache** e **MySQL**.
   - *(Dica: Se o MySQL do XAMPP acusar erro de porta 3306 ocupada, é porque há outro serviço MySQL do Windows rodando. O Petfy detecta automaticamente portas 3306/3307 e cria o banco sozinho caso não exista)*.

3. **Importar o Banco de Dados (Opcional)**:
   - Acesse: `http://localhost/phpmyadmin`
   - O sistema tenta se conectar automaticamente ao banco `petfy` ou `vidapet`. Se desejar importar manualmente, use `banco de dados/petfy_database.sql`.

4. **Acessar o Sistema**:
   - Acesse: `http://localhost/PETFY`
   - O sistema carregará a página inicial através de `front-end/index.php`.

---

## 🔑 Contas de Teste Pré-cadastradas

| Tipo | E-mail | Senha |
| :--- | :--- | :--- |
| **Usuário Tutor** | `maria@exemplo.com` | `123456` |
| **Usuário Adotante** | `carlos@exemplo.com` | `123456` |
| **Administrador** | `admin@petfy.com.br` | `123456` |

---

## ✨ Recursos Implementados

1. **Design 100% Fiel aos Prints**:
   - Tela de Login com divisão 50/50 em verde menta (`#c7f2e7`), nuvens decorativas e logo.
   - Tela de Cadastro centralizada com botão Google, bandeira do Brasil (+55) e campos estilizados.
2. **Back-End Seguro e Modular**:
   - Conexão PDO Singleton com Prepared Statements contra SQL Injection.
   - Senhas criptografadas com Bcrypt (`password_hash` e `password_verify`).
   - Sessões protegidas com regeneração de ID contra session fixation.
3. **Catálogo e Filtros Inteligentes**:
   - Filtros combinados por espécie, raça, porte, sexo e busca textual.
4. **Cadastro de Pet Multistep**:
   - 3 etapas intuitivas (Sobre o Pet, Saúde, Contato).
   - Upload de fotos com validação de extensão/tamanho e pré-visualização instantânea.
   - Busca automática de endereço por CEP via **ViaCEP**.
5. **Adoção e Comunicação**:
   - Envio de mensagem de apresentação para o tutor.
   - Painel do tutor para aprovar/recusar pedidos.
   - Botão direto para contato via WhatsApp após aprovação.
