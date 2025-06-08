<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<main class="home-main">
  <!-- Hero Section -->
  <section class="hero-section">
    <div class="hero-content">
      <div class="hero-text">
        <h1 class="hero-title">
          Bem-vindo ao <span class="highlight">Pizza Universe</span>
        </h1>
        <p class="hero-subtitle">
          Sabores que conquistam o universo, pizzas que levam você a uma galáxia de experiências únicas
        </p>
        <div class="hero-buttons">
          <?php if (isset($_SESSION['user_id'])): ?>
            <a href="?page=dashboard" class="btn-primary">
              <i class="bi bi-speedometer2"></i>
              Ir para Dashboard
            </a>
            <a href="?page=orders&action=create" class="btn-secondary">
              <i class="bi bi-plus-circle"></i>
              Fazer Pedido
            </a>
          <?php else: ?>
            <a href="?page=login" class="btn-primary">
              <i class="bi bi-box-arrow-in-right"></i>
              Fazer Login
            </a>
            <a href="#menu" class="btn-secondary">
              <i class="bi bi-arrow-down"></i>
              Ver Cardápio
            </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="hero-image">
        <img src="./assets/images/pizzaflut.png" alt="Pizza deliciosa" class="floating-pizza">
      </div>
    </div>
    <div class="hero-decoration">
      <div class="planet planet-1"></div>
      <div class="planet planet-2"></div>
      <div class="planet planet-3"></div>
    </div>
  </section>

  <!-- About Section -->
  <section class="about-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">
          <i class="bi bi-star-fill"></i>
          Nossa História
        </h2>
        <p class="section-subtitle">Uma jornada gastronômica pelo universo dos sabores</p>
      </div>

      <div class="about-grid">
        <div class="about-text">
          <h3>Desde 2024, criando experiências únicas</h3>
          <p>
            No Pizza Universe, cada pizza é uma viagem por sabores únicos e ingredientes cuidadosamente
            selecionados. Nossa missão é levar você a uma experiência gastronômica que transcende
            os limites terrestres.
          </p>
          <p>
            Com receitas desenvolvidas por nossos chefs especializados e ingredientes frescos
            entregues diariamente, garantimos que cada mordida seja uma explosão de sabor.
          </p>

          <div class="features-grid">
            <div class="feature-item">
              <i class="bi bi-award"></i>
              <span>Ingredientes Premium</span>
            </div>
            <div class="feature-item">
              <i class="bi bi-lightning"></i>
              <span>Entrega Rápida</span>
            </div>
            <div class="feature-item">
              <i class="bi bi-heart"></i>
              <span>Feito com Amor</span>
            </div>
            <div class="feature-item">
              <i class="bi bi-shield-check"></i>
              <span>Qualidade Garantida</span>
            </div>
          </div>
        </div>

        <div class="about-image">
          <div class="image-container">
            <img src="./assets/images/logo.png" alt="Pizza Universe Logo" class="about-logo">
            <div class="stats-overlay">
              <div class="stat-item">
                <span class="stat-number">1000+</span>
                <span class="stat-label">Pizzas Entregues</span>
              </div>
              <div class="stat-item">
                <span class="stat-number">50+</span>
                <span class="stat-label">Sabores Únicos</span>
              </div>
              <div class="stat-item">
                <span class="stat-number">100%</span>
                <span class="stat-label">Satisfação</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Menu Highlights -->
  <section id="menu" class="menu-highlights">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">
          <i class="bi bi-stars"></i>
          Sabores em Destaque
        </h2>
        <p class="section-subtitle">Nossas criações mais populares do universo</p>
      </div>

      <div class="menu-grid">
        <div class="menu-card traditional">
          <div class="menu-card-header">
            <i class="bi bi-heart-fill"></i>
            <h3>Tradicionais</h3>
          </div>
          <div class="menu-card-content">
            <p>Os clássicos que nunca saem de moda, preparados com nossa receita especial.</p>
            <ul class="flavors-list">
              <li>Margherita Cósmica</li>
              <li>Calabresa Galáctica</li>
              <li>Portuguesa Estelar</li>
              <li>Quatro Queijos Nebulosa</li>
            </ul>
          </div>
          <div class="menu-card-footer">
            <span class="price-range">A partir de R$ 25,90</span>
          </div>
        </div>

        <div class="menu-card special">
          <div class="menu-card-header">
            <i class="bi bi-gem"></i>
            <h3>Especiais</h3>
          </div>
          <div class="menu-card-content">
            <p>Criações únicas dos nossos chefs, sabores que só existem no nosso universo.</p>
            <ul class="flavors-list">
              <li>Supernova de Camarão</li>
              <li>Meteoro de Carne Seca</li>
              <li>Buraco Negro BBQ</li>
              <li>Via Láctea Veggie</li>
            </ul>
          </div>
          <div class="menu-card-footer">
            <span class="price-range">A partir de R$ 35,90</span>
          </div>
        </div>

        <div class="menu-card sweet">
          <div class="menu-card-header">
            <i class="bi bi-heart-eyes"></i>
            <h3>Doces</h3>
          </div>
          <div class="menu-card-content">
            <p>Para finalizar sua jornada com doçura, sobremesas que são pura magia.</p>
            <ul class="flavors-list">
              <li>Chocolate Sideral</li>
              <li>Morango Lunar</li>
              <li>Banana Espacial</li>
              <li>Nutella Orbital</li>
            </ul>
          </div>
          <div class="menu-card-footer">
            <span class="price-range">A partir de R$ 22,90</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="contact-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">
          <i class="bi bi-telephone"></i>
          Contato & Localização
        </h2>
        <p class="section-subtitle">Estamos prontos para atender você em nossa base terrestre</p>
      </div>

      <div class="contact-grid">
        <div class="contact-info">
          <h3>Informações de Contato</h3>

          <div class="contact-item">
            <i class="bi bi-geo-alt"></i>
            <div>
              <strong>Endereço</strong>
              <p>Rua das Galáxias, 123<br>Bairro Universo - São Paulo, SP</p>
            </div>
          </div>

          <div class="contact-item">
            <i class="bi bi-telephone"></i>
            <div>
              <strong>Telefone</strong>
              <p>(11) 9999-8888</p>
            </div>
          </div>

          <div class="contact-item">
            <i class="bi bi-clock"></i>
            <div>
              <strong>Horário de Funcionamento</strong>
              <p>Segunda a Domingo<br>18h00 às 23h30</p>
            </div>
          </div>

          <div class="contact-item">
            <i class="bi bi-envelope"></i>
            <div>
              <strong>E-mail</strong>
              <p>contato@pizzauniverse.com</p>
            </div>
          </div>
        </div>

        <div class="delivery-info">
          <h3>Área de Entrega</h3>
          <p class="delivery-description">
            Entregamos em toda a região metropolitana com rapidez e qualidade garantida.
          </p>

          <div class="delivery-features">
            <div class="delivery-feature">
              <i class="bi bi-truck"></i>
              <span>Entrega em até 45 minutos</span>
            </div>
            <div class="delivery-feature">
              <i class="bi bi-shield-check"></i>
              <span>Entrega segura e higiênica</span>
            </div>
            <div class="delivery-feature">
              <i class="bi bi-thermometer-half"></i>
              <span>Pizza sempre quentinha</span>
            </div>
            <div class="delivery-feature">
              <i class="bi bi-credit-card"></i>
              <span>Várias formas de pagamento</span>
            </div>
          </div>

          <div class="cta-contact">
            <a href="tel:1199998888" class="btn-contact">
              <i class="bi bi-telephone-fill"></i>
              Fazer Pedido por Telefone
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>