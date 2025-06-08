<?php

namespace App\Views;

use App\Models\Order;
use App\Models\Customer;

abstract class OrderView
{
    public static function renderCreateForm(?string $message = null, array $customers = [], array $pizzasByCategory = [], ?array $formData = null): void
    {
        $customerIdValue = $formData['customer_id'] ?? '';
        $notesValue = $formData['notes'] ?? '';
?>
        <main class="orders-container">
            <div class="orders-main">
                <h1>Novo Pedido</h1>

                <div class="orders-navigation">
                    <a href="?page=orders" class="orders-btn-back"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>

                <section class="orders-section">
                    <?php if ($message): ?>
                        <div class="orders-message error">
                            <i class="bi bi-exclamation-triangle"></i>
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="?page=orders&action=create" class="orders-form">
                        <input type="hidden" name="<?= \App\Util\CsrfToken::getTokenName() ?>" value="<?= \App\Util\CsrfToken::generate() ?>">

                        <div class="orders-form-section">
                            <h3><i class="bi bi-person-check"></i> Selecionar Cliente</h3>
                            <div class="orders-form-group">
                                <label for="customer_id">Cliente:</label>
                                <select id="customer_id" name="customer_id" required>
                                    <option value="">Selecione um cliente</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?= $customer->id ?>"
                                            data-name="<?= htmlspecialchars($customer->name) ?>"
                                            data-phone="<?= htmlspecialchars($customer->phone) ?>"
                                            data-cpf="<?= htmlspecialchars($customer->cpf) ?>"
                                            data-street="<?= htmlspecialchars($customer->street) ?>"
                                            data-neighborhood="<?= htmlspecialchars($customer->neighborhood) ?>"
                                            data-city="<?= htmlspecialchars($customer->city) ?>"
                                            data-state="<?= htmlspecialchars($customer->state) ?>"
                                            data-zipcode="<?= htmlspecialchars($customer->zipcode) ?>"
                                            <?= $customerIdValue == $customer->id ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($customer->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div id="customer-info" class="orders-customer-display" style="display: none;">
                                <div class="orders-customer-card">
                                    <div class="orders-customer-card-header">
                                        <h4><i class="bi bi-person-circle"></i> Dados do Cliente</h4>
                                    </div>
                                    <div class="orders-customer-details">
                                        <div class="orders-customer-row">
                                            <span class="orders-customer-label">Nome:</span>
                                            <span id="customer-name" class="orders-customer-value">-</span>
                                        </div>
                                        <div class="orders-customer-row">
                                            <span class="orders-customer-label">Telefone:</span>
                                            <span id="customer-phone" class="orders-customer-value">-</span>
                                        </div>
                                        <div class="orders-customer-row">
                                            <span class="orders-customer-label">CPF:</span>
                                            <span id="customer-cpf" class="orders-customer-value">-</span>
                                        </div>
                                        <div class="orders-customer-row">
                                            <span class="orders-customer-label">Endereço:</span>
                                            <div class="orders-address-details">
                                                <div id="customer-street">-</div>
                                                <div id="customer-neighborhood">-</div>
                                                <div class="orders-city-state">
                                                    <span id="customer-city">-</span> - <span id="customer-state">-</span>
                                                </div>
                                                <div class="orders-zipcode">
                                                    CEP: <span id="customer-zipcode">-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="orders-form-section">
                            <h3><i class="bi bi-grid-3x3-gap-fill"></i> Selecionar Pizzas</h3>

                            <?php foreach ($pizzasByCategory as $category => $pizzas): ?>
                                <div class="orders-category">
                                    <h4 class="orders-category-title"><i class="bi bi-grid-3x3-gap-fill"></i> <?= htmlspecialchars($category) ?></h4>
                                    <div class="orders-pizzas-grid">
                                        <?php foreach ($pizzas as $pizza): ?>
                                            <div class="orders-pizza-card" data-pizza-id="<?= $pizza->id ?>">
                                                <div class="orders-pizza-header">
                                                    <h5 class="orders-pizza-name"><?= htmlspecialchars($pizza->name) ?></h5>
                                                    <div class="orders-pizza-price">
                                                        R$ <?= number_format($pizza->price, 2, ',', '.') ?>
                                                    </div>
                                                </div>

                                                <div class="orders-pizza-body">
                                                    <p class="orders-pizza-description"><?= htmlspecialchars($pizza->description) ?></p>
                                                </div>

                                                <div class="orders-pizza-controls">
                                                    <div class="orders-quantity-controls">
                                                        <button type="button" class="orders-btn-quantity orders-btn-decrease" onclick="decreaseQuantity(<?= $pizza->id ?>)">
                                                            <i class="bi bi-dash"></i>
                                                        </button>
                                                        <div class="orders-quantity-display">
                                                            <input type="number" name="pizzas[<?= $pizza->id ?>]"
                                                                id="qty_<?= $pizza->id ?>"
                                                                class="orders-quantity-input"
                                                                value="0" min="0" max="99"
                                                                readonly>
                                                        </div>
                                                        <button type="button" class="orders-btn-quantity orders-btn-increase" onclick="increaseQuantity(<?= $pizza->id ?>)">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="orders-form-section">
                            <h3><i class="bi bi-receipt"></i> Resumo do Pedido</h3>
                            <div id="order-summary" class="orders-summary">
                                <div class="orders-summary-item">
                                    <span><i class="bi bi-cart-x"></i> Nenhuma pizza selecionada</span>
                                    <span>R$ 0,00</span>
                                </div>
                            </div>
                        </div>

                        <div class="orders-form-section">
                            <h3><i class="bi bi-chat-text"></i> Observações</h3>
                            <div class="orders-form-group">
                                <label for="notes">Observações do Pedido:</label>
                                <textarea id="notes" name="notes"
                                    placeholder="Observações especiais, preferências, etc."
                                    rows="3"><?= htmlspecialchars($notesValue) ?></textarea>
                            </div>
                        </div>

                        <button type="submit" id="submit-order" class="orders-btn-primary" disabled>
                            <i class="bi bi-lock"></i> Selecione as pizzas
                        </button>
                    </form>
                </section>
            </div>
        </main>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                function waitForScript() {
                    if (typeof initializePizzaData === 'function') {
                        initializePizzaData(
                            <?= json_encode(array_reduce(array_merge(...array_values($pizzasByCategory)), function ($carry, $pizza) {
                                $carry[$pizza->id] = $pizza->price;
                                return $carry;
                            }, [])) ?>,
                            <?= json_encode(array_reduce(array_merge(...array_values($pizzasByCategory)), function ($carry, $pizza) {
                                $carry[$pizza->id] = $pizza->name;
                                return $carry;
                            }, [])) ?>
                        );
                    } else {
                        setTimeout(waitForScript, 100);
                    }
                }

                waitForScript();
            });
        </script>
    <?php
    }

    public static function renderList(array $orders): void
    {
    ?>
        <main class="orders-container">
            <div class="orders-main">
                <h1>Gerenciar Pedidos</h1>

                <div class="orders-navigation">
                    <a href="?page=orders&action=create" class="orders-btn-primary"><i class="bi bi-plus-lg"></i> Novo Pedido</a>
                </div>

                <?php if (!empty($orders)): ?>
                    <div class="orders-table-container">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td class="orders-order-number"><?= htmlspecialchars($order->order_number) ?></td>
                                        <td class="orders-customer-info"><?= htmlspecialchars($order->customer_name ?? 'Cliente não encontrado') ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($order->created_at)) ?></td>
                                        <td>
                                            <form method="POST" action="?page=orders&action=update_status" style="display: inline;">
                                                <input type="hidden" name="<?= \App\Util\CsrfToken::getTokenName() ?>" value="<?= \App\Util\CsrfToken::generate() ?>">
                                                <input type="hidden" name="order_id" value="<?= $order->id ?>">
                                                <select name="status" class="orders-status-select" onchange="this.form.submit()">
                                                    <option value="pending" <?= $order->status === 'pending' ? 'selected' : '' ?>>Pendente</option>
                                                    <option value="confirmed" <?= $order->status === 'confirmed' ? 'selected' : '' ?>>Confirmado</option>
                                                    <option value="preparing" <?= $order->status === 'preparing' ? 'selected' : '' ?>>Preparando</option>
                                                    <option value="ready" <?= $order->status === 'ready' ? 'selected' : '' ?>>Pronto</option>
                                                    <option value="delivered" <?= $order->status === 'delivered' ? 'selected' : '' ?>>Entregue</option>
                                                    <option value="cancelled" <?= $order->status === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="orders-total-amount">R$ <?= number_format($order->total_amount, 2, ',', '.') ?></td>
                                        <td>
                                            <a href="?page=orders&action=view&id=<?= $order->id ?>" class="orders-btn-view" title="Visualizar Pedido">
                                                <i class="bi bi-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="orders-empty-state">
                        <div class="orders-empty-icon"><i class="bi bi-clipboard"></i></div>
                        <h3>Nenhum pedido encontrado</h3>
                        <p>Comece criando seu primeiro pedido.</p>
                        <a href="?page=orders&action=create" class="orders-btn-primary">Criar Primeiro Pedido</a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    <?php
    }

    public static function renderDetails(Order $order, Customer $customer, array $orderItems): void
    {
    ?>
        <main class="orders-container">
            <div class="orders-details">
                <div class="orders-navigation">
                    <a href="?page=orders" class="orders-btn-back"><i class="bi bi-arrow-left"></i> Voltar aos Pedidos</a>
                </div>

                <div class="orders-header">
                    <div class="orders-info">
                        <h2>Pedido <?= htmlspecialchars($order->order_number) ?></h2>
                        <p class="orders-date">Criado em <?= date('d/m/Y às H:i', strtotime($order->created_at)) ?></p>
                    </div>
                    <div class="orders-status">
                        <label for="status">Status:</label>
                        <form method="POST" action="?page=orders&action=update_status" style="display: inline;">
                            <input type="hidden" name="<?= \App\Util\CsrfToken::getTokenName() ?>" value="<?= \App\Util\CsrfToken::generate() ?>">
                            <input type="hidden" name="order_id" value="<?= $order->id ?>">
                            <input type="hidden" name="redirect" value="view&id=<?= $order->id ?>">
                            <select name="status" class="orders-status-select" onchange="this.form.submit()">
                                <option value="pending" <?= $order->status === 'pending' ? 'selected' : '' ?>>Pendente</option>
                                <option value="confirmed" <?= $order->status === 'confirmed' ? 'selected' : '' ?>>Confirmado</option>
                                <option value="preparing" <?= $order->status === 'preparing' ? 'selected' : '' ?>>Preparando</option>
                                <option value="ready" <?= $order->status === 'ready' ? 'selected' : '' ?>>Pronto</option>
                                <option value="delivered" <?= $order->status === 'delivered' ? 'selected' : '' ?>>Entregue</option>
                                <option value="cancelled" <?= $order->status === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="orders-content">
                    <div class="orders-customer-section">
                        <h3>Informações do Cliente</h3>
                        <p><strong>Nome:</strong> <?= htmlspecialchars($customer->name) ?></p>
                        <p><strong>Telefone:</strong> <?= htmlspecialchars($customer->phone) ?></p>
                        <p><strong>Endereço:</strong><br>
                            <?= htmlspecialchars($customer->street) ?><br>
                            <?= htmlspecialchars($customer->neighborhood) ?><br>
                            <?= htmlspecialchars($customer->city) ?> - <?= htmlspecialchars($customer->state) ?><br>
                            CEP: <?= htmlspecialchars($customer->zipcode) ?>
                        </p>
                        <?php if (!empty($order->delivery_address)): ?>
                            <p><strong>Endereço de Entrega:</strong><br>
                                <?= nl2br(htmlspecialchars($order->delivery_address)) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="orders-items-section">
                        <h3>Itens do Pedido</h3>
                        <table class="orders-items-table">
                            <thead>
                                <tr>
                                    <th>Pizza</th>
                                    <th>Quantidade</th>
                                    <th>Preço Unitário</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item->pizza_name) ?></td>
                                        <td><?= $item->quantity ?></td>
                                        <td>R$ <?= number_format($item->unit_price, 2, ',', '.') ?></td>
                                        <td>R$ <?= number_format($item->subtotal, 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="orders-final-total">
                            <strong>Total: R$ <?= number_format($order->total_amount, 2, ',', '.') ?></strong>
                        </div>
                    </div>

                    <?php if (!empty($order->notes)): ?>
                        <div class="orders-notes-section">
                            <h3>Observações</h3>
                            <p><?= nl2br(htmlspecialchars($order->notes)) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
<?php
    }
}
