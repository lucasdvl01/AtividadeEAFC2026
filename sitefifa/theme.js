(function() {
    // Aplica o tema imediatamente para evitar piscadas na tela
    const savedTheme = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
})();

document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('theme-toggle');
    if (!toggleBtn) return;

    const currentTheme = document.documentElement.getAttribute('data-theme');
    atualizarBotao(toggleBtn, currentTheme);

    toggleBtn.addEventListener('click', () => {
        const themeAtual = document.documentElement.getAttribute('data-theme');
        const novoTema = themeAtual === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', novoTema);
        localStorage.setItem('theme', novoTema);
        atualizarBotao(toggleBtn, novoTema);
    });
});

function atualizarBotao(btn, tema) {
    if (tema === 'dark') {
        btn.innerHTML = '<span>☀️</span> <span>Modo Claro</span>';
    } else {
        btn.innerHTML = '<span>🌙</span> <span>Modo Escuro</span>';
    }
}