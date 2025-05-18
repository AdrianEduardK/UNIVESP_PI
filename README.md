# Green Manufacturing: Digitalização e Automação de Processos Documentais

<p align="center">
<a href="https://www.univesp.br/"><img src="https://univesp.br/static/img/logo-univesp.png" alt="UNIVESP - Universidade Virtual do Estado de São Paulo" border="0" width="300"></a>
</p>

## 👥 Integrantes
- Adrian Eduard Justo Sillex Zimmermann
- Amanda Fragnan de Oliveira
- André Cicero Nunes Silva
- Diego Aparecido Leite de Faria
- Juares da Silva de Jesus
- Riane Miranda de Franca
- Robson Cruz
- Vitor Gabriel da Cruz

## 👩‍🏫 Orientação
- **Tutora:** Laura Parisi
- **Polo:** Itapecerica da Serra/Juquitiba

## 📜 Descrição

### 🎯 Objetivo
Desenvolvimento de um sistema web com banco de dados para digitalização e automação de processos documentais na empresa **BORKAR Acessórios Automotivos Originais**, visando:
- Eliminar formulários físicos
- Reduzir em 60% as não conformidades documentais
- Promover práticas sustentáveis (Green Manufacturing)

### 🔍 Problema Identificado
- 60% das não conformidades (2º sem/2024) relacionadas a falhas no fluxo documental
- Processos 100% manuais com:
  - Alto consumo de papel/tinta
  - Erros de preenchimento
  - Dificuldade de recuperação de documentos
  - Riscos em auditorias

## ⚙️ Arquitetura da Solução

### 🛠️ Tecnologias
| Componente       | Tecnologia          |
|------------------|---------------------|
| Front-end        | HTML5, CSS          |
| Back-end         | PHP                 |
| Banco de Dados   | MySQL               |
| Ambiente         | XAMPP               |
| Versionamento    | GitHub              |

### 📊 Fluxograma do Sistema
```mermaid
graph TD
    A[Operador] -->|Preenche formulário digital| B[Validação Automática]
    B --> C[Armazenamento no BD]
    C --> D[PCP: Análise/Aprovação]
    D --> E[Histórico em Nuvem]
