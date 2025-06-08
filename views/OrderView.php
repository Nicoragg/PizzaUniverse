<?php

namespace App\Views;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Pizza;

abstract class OrderView
{
    public static function renderCreateForm(?string $message = null, array $customers = [], array $pizzasByCategory = [], ?array $formData = null): void
    {
        $customerIdValue = $formData['customer_id'] ?? '';
        $deliveryAddressValue = $formData['delivery_address'] ?? '';
        $notesValue = $formData['notes'] ?? '';
?>
        <main>
            <h1>Novo Pedido</h1>

            <div class="navigation-buttons">
                <a href="?page=orders"><i class="bi bi-arrow-left"></i> Voltar</a>
            </div>

            <section>
                <?php if ($message): ?>
                    <p class="message error">
                        <?= htmlspecialchars($message) ?>
                    </p>
                <?php endif; ?>

                <form method="POST" action="?page=orders&action=create" class="order-form">
                    <input type="hidden" name="<?= \App\Util\CsrfToken::getTokenName() ?>" value="<?= \App\Util\CsrfToken::generate() ?>">

                    <!-- Seleção do Cliente -->
                    <div class="form-section">
                        <h3>Informações do Cliente</h3>
                        <div class="form-group">
                            <label for="customer_id">Cliente:</label>
                            <select id="customer_id" name="customer_id" required>
                                <option value="">Selecione um cliente</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer->id ?>" <?= $customerIdValue == $customer->id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($customer->name) ?> - <?= htmlspecialchars($customer->phone) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="delivery_address">Endereço de Entrega:</label>
                            <textarea id="delivery_address" name="delivery_address"
                                placeholder="Digite o endereço completo para entrega"
                                rows="3"><?= htmlspecialchars($deliveryAddressValue) ?></textarea>
                        </div>
                    </div>

                    <!-- Seleção de Pizzas -->
                    <div class="form-section">
                        <h3>Selecionar Pizzas</h3>

                        <?php foreach ($pizzasByCategory as $category => $pizzas): ?>
                            <div class="category-section">
                                <h4><?= htmlspecialchars($category) ?></h4>
                                <div class="pizzas-grid">
                                    <?php foreach ($pizzas as $pizza): ?>
                                        <div class="pizza-item">
                                            <div class="pizza-info">
                                                <h5><?= htmlspecialchars($pizza->name) ?></h5>
                                                <p><?= htmlspecialchars($pizza->description) ?></p>
                                                <div class="pizza-price">R$ <?= number_format($pizza->price, 2, ',', '.') ?></div>
                                            </div>
                                            <div class="pizza-controls">
                                                <button type="button" class="btn-quantity" onclick="decreaseQuantity(<?= $pizza->id ?>)">-</button>
                                                <input type="number" name="pizzas[<?= $pizza->id ?>]"
                                                    id="qty_<?= $pizza->id ?>"
                                                    class="quantity-input"
                                                    value="0" min="0" max="99">
                                                <button type="button" class="btn-quantity" onclick="increaseQuantity(<?= $pizza->id ?>)">+</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Resumo do Pedido -->
                    <div class="form-section">
                        <h3>Resumo do Pedido</h3>
                        <div id="order-summary">
                            <div class="summary-item">
                                <span>Nenhuma pizza selecionada</span>
                                <span>R$ 0,00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="form-section">
                        <h3>Observações</h3>
                        <div class="form-group">
                            <label for="notes">Observações do Pedido:</label>
                            <textarea id="notes" name="notes"
                                placeholder="Observações especiais, preferências, etc."
                                rows="3"><?= htmlspecialchars($notesValue) ?></textarea>
                        </div>
                    </div>

                    <button type="submit" id="submit-order" disabled>Finalizar Pedido</button>
                </form>
            </section>
        </main>

        <script>
            const pizzaPrices = <?= json_encode(array_reduce(array_merge(...array_values($pizzasByCategory)), function ($carry, $pizza) {
                                    $carry[$pizza->id] = $pizza->price;
                                    return $carry;
                                }, [])) ?>;

            const pizzaNames = <?= json_encode(array_reduce(array_merge(...array_values($pizzasByCategory)), function ($carry, $pizza) {
                                    $carry[$pizza->id] = $pizza->name;
                                    return $carry;
                                }, [])) ?>;

            function increaseQuantity(pizzaId) {
                const input = document.getElementById('qty_' + pizzaId);
                const currentValue = parseInt(input.value);
                if (currentValue < 99) {
                    input.value = currentValue + 1;
                    updateOrderSummary();
                }
            }

            function decreaseQuantity(pizzaId) {
                const input = document.getElementById('qty_' + pizzaId);
                const currentValue = parseInt(input.value);
                if (currentValue > 0) {
                    input.value = currentValue - 1;
                    updateOrderSummary();
                }
            }

            function updateOrderSummary() {
                const summaryDiv = document.getElementById('order-summary');
                const submitBtn = document.getElementById('submit-order');
                let total = 0;
                let items = [];

                // Percorrer todas as inputs de quantidade
                document.querySelectorAll('.quantity-input').forEach(input => {
                    const pizzaId = input.id.replace('qty_', '');
                    const quantity = parseInt(input.value);

                    if (quantity > 0) {
                        const price = pizzaPrices[pizzaId];
                        const name = pizzaNames[pizzaId];
                        const subtotal = price * quantity;

                        items.push({
                            name: name,
                            quantity: quantity,
                            price: price,
                            subtotal: subtotal
                        });

                        total += subtotal;
                    }
                });

                // Atualizar o resumo
                if (items.length === 0) {
                    summaryDiv.innerHTML = '<div class="summary-item"><span>Nenhuma pizza selecionada</span><span>R$ 0,00</span></div>';
                    submitBtn.disabled = true;
                } else {
                    let html = '';
                    items.forEach(item => {
                        html += `<div class="summary-item">
                            <span>${item.quantity}x ${item.name}</span>
                            <span>R$ ${item.subtotal.toFixed(2).replace('.', ',')}</span>
                        </div>`;
                    });
                    html += `<div class="summary-item total">
                        <span>Total</span>
                        <span>R$ ${total.toFixed(2).replace('.', ',')}</span>
                    </div>`;

                    summaryDiv.innerHTML = html;
                    submitBtn.disabled = false;
                }
            }

            // Adicionar event listeners para inputs manuais
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('input', updateOrderSummary);
            });
        </script>
    <?php
    }

    public static function renderList(array $orders): void
    {
    ?>
        <main>
            <h1>Gerenciar Pedidos</h1>

            <div class="navigation-buttons">
                <a href="?page=orders&action=create" class="btn-primary"><i class="bi bi-plus-lg"></i> Novo Pedido</a>
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
                                <tr class="order-row">
                                    <td class="order-number"><?= htmlspecialchars($order->order_number) ?></td>
                                    <td class="customer-info"><?= htmlspecialchars($order->customer_name ?? 'Cliente não encontrado') ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($order->created_at)) ?></td>
                                    <td>
                                        <form method="POST" action="?page=orders&action=update_status" style="display: inline;">
                                            <input type="hidden" name="<?= \App\Util\CsrfToken::getTokenName() ?>" value="<?= \App\Util\CsrfToken::generate() ?>">
                                            <input type="hidden" name="order_id" value="<?= $order->id ?>">
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                <option value="pending" <?= $order->status === 'pending' ? 'selected' : '' ?>>Pendente</option>
                                                <option value="confirmed" <?= $order->status === 'confirmed' ? 'selected' : '' ?>>Confirmado</option>
                                                <option value="preparing" <?= $order->status === 'preparing' ? 'selected' : '' ?>>Preparando</option>
                                                <option value="ready" <?= $order->status === 'ready' ? 'selected' : '' ?>>Pronto</option>
                                                <option value="delivered" <?= $order->status === 'delivered' ? 'selected' : '' ?>>Entregue</option>
                                                <option value="cancelled" <?= $order->status === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="order-total">R$ <?= number_format($order->total_amount, 2, ',', '.') ?></td>
                                    <td>
                                        <a href="?page=orders&action=view&id=<?= $order->id ?>" class="btn-view" title="Visualizar Pedido">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <h3>Nenhum pedido encontrado</h3>
                    <p>Comece criando seu primeiro pedido.</p>
                    <a href="?page=orders&action=create" class="btn-primary">Criar Primeiro Pedido</a>
                </div>
            <?php endif; ?>
        </main>
    <?php
    }

    public static function renderDetails(Order $order, Customer $customer, array $orderItems): void
    {
    ?>
        <main class="order-details">
            <div class="navigation-buttons">
                <a href="?page=orders"><i class="bi bi-arrow-left"></i> Voltar aos Pedidos</a>
            </div>

            <div class="order-header">
                <div class="order-info">
                    <h2>Pedido <?= htmlspecialchars($order->order_number) ?></h2>
                    <p class="order-date">Criado em <?= date('d/m/Y às H:i', strtotime($order->created_at)) ?></p>
                </div>
                <div class="order-status">
                    <label for="status">Status:</label>
                    <form method="POST" action="?page=orders&action=update_status" style="display: inline;">
                        <input type="hidden" name="<?= \App\Util\CsrfToken::getTokenName() ?>" value="<?= \App\Util\CsrfToken::generate() ?>">
                        <input type="hidden" name="order_id" value="<?= $order->id ?>">
                        <input type="hidden" name="redirect" value="view&id=<?= $order->id ?>">
                        <select name="status" class="status-select" onchange="this.form.submit()">
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

            <div class="order-content">
                <div class="customer-section">
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

                <div class="items-section">
                    <h3>Itens do Pedido</h3>
                    <table class="items-table">
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
                    <div class="order-total">
                        <strong>Total: R$ <?= number_format($order->total_amount, 2, ',', '.') ?></strong>
                    </div>
                </div>

                <?php if (!empty($order->notes)): ?>
                    <div class="notes-section">
                        <h3>Observações</h3>
                        <p><?= nl2br(htmlspecialchars($order->notes)) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
<?php
    }
}
