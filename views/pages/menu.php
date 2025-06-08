<main class="container">
  <section class="menu-section">
    <h1 class="menu-title">Cardápio de Pizzas</h1>

    <div class="menu-category">
      <h2>Tradicionais</h2>
      <ul>
        <?php
        foreach ($pizzasByCategory['tradicionais'] as $pizza) {
          echo "<li>" . htmlspecialchars($pizza['nome']) . "</li>";
        }
        ?>
      </ul>
    </div>

    <div class="menu-category">
      <h2>Especiais</h2>
      <ul>
        <?php
        foreach ($pizzasByCategory['especiais'] as $pizza) {
          echo "<li>" . htmlspecialchars($pizza['nome']) . "</li>";
        }
        ?>
      </ul>
    </div>

    <div class="menu-category">
      <h2>Doces</h2>
      <ul>
        <?php
        foreach ($pizzasByCategory['doces'] as $pizza) {
          echo "<li>" . htmlspecialchars($pizza['nome']) . "</li>";
        }
        ?>
      </ul>
    </div>
  </section>
</main>