document.getElementById("btnEntrar").addEventListener("click", function () {
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const erro = document.getElementById("mensagemErro");

    // Exemplo de validação simples
    const emailCorreto = "admin@invemed.com";
    const passwordCorreta = "1234";

    if (email === "" || password === "") {
        erro.textContent = "Preencha todos os campos.";
        erro.classList.remove("d-none");
        return;
    }

    if (email !== emailCorreto || password !== passwordCorreta) {
        const erro = document.getElementById("mensagemErro");
        erro.classList.remove("d-none");
        return;
    }

    // Se estiver tudo certo, redireciona
    erro.classList.add("d-none");
    window.location.href = "index.html";
});

// Esconde o erro quando o utilizador começa a escrever
document.querySelectorAll("#email, #password").forEach(input => {
    input.addEventListener("input", () => {
        document.getElementById("mensagemErro").classList.add("d-none");
    });
});
