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
                                                <span id="customer-street">-</span>,
                                                <span id="customer-neighborhood">-</span>,
                                                <span id="customer-city">-</span> - <span id="customer-state">-</span>,
                                                CEP: <span id="customer-zipcode">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="orders-form-section">
                            <h3><i class="bi bi-grid-3x3-gap-fill"></i> Selecionar Pizzas</h3>

                            <div class="orders-pizza-filters">
                                <h4><i class="bi bi-funnel"></i> Filtrar Pizzas</h4>

                                <div class="orders-filter-controls">
                                    <div class="orders-filter-group">
                                        <label for="orders-filter-search">Buscar por nome:</label>
                                        <input type="text" id="orders-filter-search" class="orders-filter-search"
                                            placeholder="Digite para buscar pizzas..." />
                                    </div>

                                    <div class="orders-filter-group">
                                        <label for="orders-filter-category">Por categoria:</label>
                                        <select id="orders-filter-category" class="orders-filter-category">
                                            <option value="">Todas</option>
                                            <?php foreach ($pizzasByCategory as $category => $pizzas): ?>
                                                <option value="<?= htmlspecialchars($category) ?>">
                                                    <?= htmlspecialchars($category) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <button type="button" id="orders-filter-clear" class="orders-filter-clear">
                                        <i class="bi bi-x"></i> Limpar
                                    </button>
                                </div>

                                <div id="orders-filter-results" class="orders-filter-results"></div>
                            </div>

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
                                                        <button type="button" class="orders-btn-quantity orders-btn-decrease">
                                                            <i class="bi bi-dash"></i>
                                                        </button>
                                                        <div class="orders-quantity-display">
                                                            <input type="number" name="pizzas[<?= $pizza->id ?>]"
                                                                id="qty_<?= $pizza->id ?>"
                                                                class="orders-quantity-input"
                                                                value="0" min="0" max="99"
                                                                readonly>
                                                        </div>
                                                        <button type="button" class="orders-btn-quantity orders-btn-increase">
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

                        <div class="orders-submit-container" id="submit-container">
                            <div class="orders-submit-text disabled" id="submit-text">
                                <i class="bi bi-info-circle"></i> Selecione pelo menos uma pizza para continuar
                            </div>
                            <button type="submit" id="submit-order" class="orders-btn-submit" disabled>
                                <i class="bi bi-lock-fill"></i> Finalizar Pedido
                            </button>
                        </div>
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

    public static function renderList(array $orders, ?string $message = null): void
    {
        $csrfToken = \App\Util\CsrfToken::generate();
        $csrfTokenName = \App\Util\CsrfToken::getTokenName();
    ?>
        <main class="orders-container">
            <div class="orders-main">
                <h1>Gerenciar Pedidos</h1>

                <div class="orders-navigation">
                    <a href="?page=orders&action=create" class="orders-btn-primary"><i class="bi bi-plus-lg"></i> Novo Pedido</a>
                </div>

                <?php if ($message): ?>
                    <div class="orders-message error">
                        <i class="bi bi-exclamation-triangle"></i>
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

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
                                            <?php
                                            $statusLabels = [
                                                'pending' => 'Pendente',
                                                'confirmed' => 'Confirmado',
                                                'preparing' => 'Preparando',
                                                'ready' => 'Pronto',
                                                'delivered' => 'Entregue',
                                                'cancelled' => 'Cancelado'
                                            ];

                                            $tempOrder = new \App\Models\Order(
                                                $order->id,
                                                $order->customer_id ?? $order->customerId,
                                                $order->order_number,
                                                $order->status,
                                                $order->total_amount,
                                                $order->delivery_address,
                                                $order->notes
                                            );
                                            $availableTransitions = $tempOrder->getAvailableTransitions();
                                            ?>

                                            <?php if (!empty($availableTransitions) && !in_array($order->status, ['delivered', 'cancelled'])): ?>
                                                <form method="POST" action="?page=orders&action=update_status" style="display: inline;">
                                                    <input type="hidden" name="<?= $csrfTokenName ?>" value="<?= $csrfToken ?>">
                                                    <input type="hidden" name="order_id" value="<?= $order->id ?>">
                                                    <select name="status" class="orders-status-select" onchange="this.form.submit()">
                                                        <option value="<?= $order->status ?>" selected><?= $statusLabels[$order->status] ?></option>
                                                        <?php foreach ($availableTransitions as $statusValue): ?>
                                                            <option value="<?= $statusValue ?>"><?= $statusLabels[$statusValue] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </form>
                                            <?php else: ?>
                                                <span class="status-badge status-<?= $order->status ?>">
                                                    <?= $statusLabels[$order->status] ?>
                                                </span>
                                            <?php endif; ?>
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

    public static function renderDetails(Order $order, Customer $customer, array $orderItems, ?string $message = null): void
    {
    ?>
        <main class="orders-container">
            <div class="orders-details" style="margin-top: 20px;">
                <div class="orders-navigation">
                    <a href="?page=orders" class="orders-btn-back"><i class="bi bi-arrow-left"></i> Voltar aos Pedidos</a>
                </div>

                <?php if ($message): ?>
                    <div class="orders-message error">
                        <i class="bi bi-exclamation-triangle"></i>
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <div class="orders-header">
                    <div class="orders-info">
                        <h2>Pedido <?= htmlspecialchars($order->order_number) ?></h2>
                        <p class="orders-date">Criado em <?= date('d/m/Y \à\s H:i', strtotime($order->created_at)) ?></p>
                    </div>
                    <div class="orders-status">
                        <label for="status">Status:</label>
                        <?php
                        $statusLabels = [
                            'pending' => 'Pendente',
                            'confirmed' => 'Confirmado',
                            'preparing' => 'Preparando',
                            'ready' => 'Pronto',
                            'delivered' => 'Entregue',
                            'cancelled' => 'Cancelado'
                        ];

                        $availableTransitions = $order->getAvailableTransitions();
                        ?>

                        <?php if (!empty($availableTransitions) && !in_array($order->status, ['delivered', 'cancelled'])): ?>
                            <form method="POST" action="?page=orders&action=update_status" style="display: inline;">
                                <input type="hidden" name="<?= \App\Util\CsrfToken::getTokenName() ?>" value="<?= \App\Util\CsrfToken::generate() ?>">
                                <input type="hidden" name="order_id" value="<?= $order->id ?>">
                                <input type="hidden" name="redirect" value="view&id=<?= $order->id ?>">
                                <select name="status" class="orders-status-select" onchange="this.form.submit()">
                                    <option value="<?= $order->status ?>" selected><?= $statusLabels[$order->status] ?></option>
                                    <?php foreach ($availableTransitions as $statusValue): ?>
                                        <option value="<?= $statusValue ?>"><?= $statusLabels[$statusValue] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        <?php else: ?>
                            <span class="status-badge status-<?= $order->status ?>">
                                <?= $statusLabels[$order->status] ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="orders-content">
                    <div class="orders-customer-section">
                        <h3><i class="bi bi-person-circle"></i> Informações do Cliente</h3>

                        <div class="orders-customer-details">
                            <div class="orders-customer-row">
                                <span class="orders-customer-label"><i class="bi bi-person"></i> Nome:</span>
                                <span class="orders-customer-value"><?= htmlspecialchars($customer->name) ?></span>
                            </div>

                            <div class="orders-customer-row">
                                <span class="orders-customer-label"><i class="bi bi-telephone"></i> Telefone:</span>
                                <span class="orders-customer-value"><?= htmlspecialchars($customer->phone) ?></span>
                            </div>

                            <?php if (!empty($customer->cpf)): ?>
                                <div class="orders-customer-row">
                                    <span class="orders-customer-label"><i class="bi bi-card-text"></i> CPF:</span>
                                    <span class="orders-customer-value"><?= htmlspecialchars($customer->cpf) ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="orders-customer-row">
                                <span class="orders-customer-label"><i class="bi bi-geo-alt"></i> Endereço:</span>
                                <div class="orders-address-details">
                                    <?= htmlspecialchars($customer->street) ?>, <?= htmlspecialchars($customer->neighborhood) ?>, <?= htmlspecialchars($customer->city) ?> - <?= htmlspecialchars($customer->state) ?>, CEP: <?= htmlspecialchars($customer->zipcode) ?>
                                </div>
                            </div>

                            <?php if (!empty($order->delivery_address)): ?>
                                <div class="orders-customer-row">
                                    <span class="orders-customer-label"><i class="bi bi-truck"></i> Entrega:</span>
                                    <div class="orders-address-details">
                                        <?= nl2br(htmlspecialchars($order->delivery_address)) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
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
                    <?php if (!empty($order->__get('notes'))): ?>
                        <div class="orders-notes-section">
                            <h3>Observações</h3>
                            <p><?= nl2br(htmlspecialchars($order->__get('notes'))) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
<?php
    }
}
