# 📦 Gestor de Inventário Pró - SGFP

Este é um sistema robusto para controlo de stock e gestão de mercadorias, desenvolvido em **PHP** e **MySQL**. Foi concebido para pequenas e médias empresas que necessitam de uma visão clara sobre as suas entradas, saídas e níveis de inventário em tempo real.

## 🚀 Funcionalidades Principais

### Gestão de Artigos:
- **Catálogo Digital:** Registo detalhado de produtos com categoria, preço de custo e preço de venda.
- **Alertas de Stock Baixo:** Indicadores visuais automáticos quando um artigo atinge o nível mínimo de segurança.
- **Controlo de Entradas/Saídas:** Registo histórico de todas as movimentações de stock para auditoria.

### Painel Administrativo (Dashboard):
- **Estatísticas de Inventário:** Gráficos (Chart.js) que mostram a valorização total do stock e os produtos mais vendidos.
- **Gestão de Fornecedores:** Base de dados de contactos para reposição rápida de mercadoria.
- **Exportação de Listagens:** Geração de relatórios de inventário em formato PDF para contagens físicas.



## 🛠️ Tecnologias Utilizadas
- **Backend:** PHP 8.x (Arquitetura Modular)
- **Base de Dados:** MySQL (Relacional)
- **Frontend:** Bootstrap 5 & FontAwesome (Interface Moderna)
- **Visualização:** Chart.js (Gráficos Dinâmicos)

## 📦 Como Instalar e Configurar

1. **Base de Dados:**
   - Importe o ficheiro `inventario_db.sql` para o seu servidor MySQL através do phpMyAdmin.
   
2. **Configuração de Ligação:**
   - Edite o ficheiro `config.php` com os dados do seu ambiente local (host, utilizador, password).

3. **Utilização Inicial:**
   - Aceda ao sistema via `localhost` e utilize as credenciais de administrador predefinidas para começar a registar categorias e produtos.

## 📐 Estrutura do Repositório
O projeto está organizado de forma a separar a lógica de processamento de dados da interface de utilizador, facilitando a escalabilidade e manutenção do código.

---
**Desenvolvido por:** [O TEU NOME AQUI]