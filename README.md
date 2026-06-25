# CodeGames 2.0

Uma plataforma de e-commerce moderna para venda de jogos digitais, construída com PHP puro (sem frameworks pesados), utilizando o padrão MVC e focada em performance e design moderno.

## 🚀 Tecnologias e Stack

* **Backend:** PHP 8.x (Estrutura MVC Nativa)
* **Frontend:** HTML5, Tailwind CSS (via CDN) e Alpine.js (para interatividade).
* **Banco de Dados:** SQLite (padrão local para facilidade de setup) e MySQL (suporte via variáveis de ambiente para Produção).
* **Roteamento:** Roteador customizado e simples (`public/index.php`).

## ⚙️ Funcionalidades Implementadas

O sistema já contempla um conjunto sólido de funcionalidades essenciais para um e-commerce:

### 1. Autenticação e Autorização (RBAC)
* Sistema completo de Login e Registro.
* Controle de sessão seguro.
* **Role-Based Access Control (RBAC):** Diferenciação entre usuários comuns (`USER`) e administradores (`ADMIN`).
* Middleware de proteção de rotas administrativas.

### 2. Área Administrativa (Painel Admin)
* **Gestão de Produtos:** CRUD completo (Criar, Ler, Atualizar, Deletar) para os jogos oferecidos na loja. Possibilidade de configurar título, preço, preço original (para exibir descontos visuais), plataforma (Steam, Epic, etc) e estoque.
* **Gestão de Cupons:** CRUD completo para criação e administração de cupons de desconto.
  * Suporte a descontos *Fixos* (R$) ou *Percentuais* (%).
  * Limitação de uso (ex: cupom válido apenas 100 vezes).
  * Data de expiração (validade).

### 3. Loja e Experiência do Usuário (Frontend)
* **Design Moderno:** Interface "Glassmorphism", dark-mode nativo inspirado em interfaces gamer premium (fundo escuro, gradientes sutis, efeitos de blur).
* **Catálogo de Jogos:** Vitrine na página inicial com os jogos disponíveis, exibição dinâmica de preços e descontos aplicados.
* **Página de Produto:** Visualização de detalhes específicos de um jogo.

### 4. Carrinho de Compras e Checkout
* **Gerenciamento de Carrinho:** Adição e remoção de produtos usando um modelo dinâmico baseado na sessão (`cart_session`), persistido no banco de dados.
* **Motor de Cupons:**
  * Capacidade do usuário de inserir cupons no carrinho.
  * Cálculos automáticos de subtotal, desconto aplicado e valor final.
  * Validações restritas (bloqueia cupons expirados ou sem limite de uso restante).

## 📂 Estrutura do Projeto

A arquitetura do projeto segue um padrão **Model-View-Controller (MVC)** adaptado para uma abordagem minimalista.

```text
├── public/                 # Pasta exposta ao servidor web (Ponto de entrada)
│   ├── index.php           # Roteador principal e entrypoint
├── src/                    # Código-fonte da aplicação (Lógica)
│   ├── Config/             # Configurações gerais (Ex: Database.php)
│   ├── Controllers/        # Controladores (AdminController, AuthController, CartController)
│   ├── Models/             # Modelos de abstração do Banco de Dados (ProductModel, CouponModel)
├── views/                  # Arquivos de visualização (HTML misturado com PHP)
│   ├── admin/              # Telas restritas do administrador
│   ├── auth/               # Telas de login e cadastro
│   ├── checkout/           # Tela do carrinho de compras
│   ├── layouts/            # Componentes reutilizáveis (header.php, footer.php)
│   ├── home.php            # Tela inicial
│   └── product.php         # Tela de detalhes de produto
├── database.sql            # Script de inicialização das tabelas
└── README.md               # Este arquivo de documentação
```

## 🛠️ Como Executar o Projeto Localmente

1. **Requisitos:** PHP 8.1+ com extensão SQLite (`pdo_sqlite`).
2. **Banco de dados:** O projeto possui rotinas para a auto-criação do banco `database.sqlite` via PDO.
3. **Servidor Embutido PHP:** 
   Execute o seguinte comando no terminal, dentro da pasta raiz do projeto:
   
   ```bash
   php -S localhost:8000 -t public
   ```
4. **Acesso:** Abra o navegador em `http://localhost:8000`.

*Observação para Vercel:* O sistema está preparado para deploy na Vercel (rodando PHP via Vercel Serverless Functions com Bref ou similar), no entanto, nesses ambientes serverless, exige-se o uso de um banco de dados externo como o MySQL configurando as variáveis no `.env` (`DB_HOST`, `DB_NAME`, etc.).
