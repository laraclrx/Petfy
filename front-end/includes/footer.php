<?php
/**
 * ============================================================
 * Petfy – FRONT-END: Rodapé Global da Aplicação
 * Toda Patinha merece um lar!
 * ============================================================
 */

if (!isset($rootPath)) {
    $rootPath = file_exists('css/style.css') ? '' : '../';
}
?>
</main>

<footer class="footer-petfy">
    <div class="footer-container">
        <div class="footer-brand">
            <div class="footer-logo">
                <span class="footer-logo-icon"><i class="fas fa-paw"></i></span>
                <span class="footer-logo-text">petfy</span>
            </div>
            <p class="footer-tagline">Toda Patinha merece um lar!</p>
            <p class="footer-desc">
                Facilitamos a conexão entre animais resgatados e famílias dispostas a oferecer um novo lar com amor, segurança e responsabilidade.
            </p>
            <div class="footer-social">
                <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                <a href="https://whatsapp.com" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>

        <div class="footer-column">
            <h4>Adoção Responsável</h4>
            <ul>
                <li><a href="<?= $rootPath ?>pages/pets.php?especie=1"><i class="fas fa-dog"></i> Cães para Adoção</a></li>
                <li><a href="<?= $rootPath ?>pages/pets.php?especie=2"><i class="fas fa-cat"></i> Gatos para Adoção</a></li>
                <li><a href="<?= $rootPath ?>pages/pets.php"><i class="fas fa-search"></i> Conhecer Todos os Pets</a></li>
                <li><a href="<?= $rootPath ?>pages/cadastrar-pet.php"><i class="fas fa-plus"></i> Cadastrar Animal</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h4>Navegação</h4>
            <ul>
                <li><a href="<?= $rootPath ?>index.php#sobre">Sobre o Projeto Petfy</a></li>
                <li><a href="<?= $rootPath ?>index.php#como-funciona">Como Funciona a Adoção</a></li>
                <li><a href="<?= $rootPath ?>pages/meus-pets.php">Painel do Tutor</a></li>
                <li><a href="<?= $rootPath ?>pages/minhas-adocoes.php">Minhas Adoções</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h4>Suporte & Dúvidas</h4>
            <ul>
                <li><a href="mailto:contato@petfy.com.br"><i class="fas fa-envelope"></i> contato@petfy.com.br</a></li>
                <li><a href="<?= $rootPath ?>index.php#faq"><i class="fas fa-question-circle"></i> Perguntas Frequentes</a></li>
                <li><a href="#"><i class="fas fa-shield-alt"></i> Termos de Uso</a></li>
                <li><a href="#"><i class="fas fa-lock"></i> Política de Privacidade</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> <strong>Petfy</strong> – Toda Patinha merece um lar! Desenvolvido para a feira / apresentação acadêmica de adoção responsável.</p>
    </div>
</footer>

<!-- Scripts Globais -->
<script src="<?= $rootPath ?>js/main.js"></script>
<?php if (isset($extraJs)): ?>
    <?php foreach ($extraJs as $jsFile): ?>
        <script src="<?= $rootPath . $jsFile ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
