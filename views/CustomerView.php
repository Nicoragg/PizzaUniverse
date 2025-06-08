<?php

namespace App\Views;

use App\Models\Customer;

abstract class CustomerView
{
    public static function renderForm(?string $message = null, ?Customer $customer = null, ?array $fieldsWithErrors = null, ?array $formData = null): void
    {
        $isEdit = $customer !== null;
        $title = $isEdit ? "Editar Cliente" : "Novo Cliente";
        $action = $isEdit ? "?page=customers&action=edit" : "?page=customers&action=create";

        $nameValue = $formData['name'] ?? ($customer ? htmlspecialchars($customer->name) : '');
        $cpfValue = $formData['cpf'] ?? ($customer ? htmlspecialchars($customer->cpf) : '');
        $phoneValue = $formData['phone'] ?? ($customer ? htmlspecialchars($customer->phone) : '');
        $statusValue = $formData['status'] ?? ($customer ? $customer->status : 'active');
        $zipcodeValue = $formData['zipcode'] ?? ($customer ? htmlspecialchars($customer->zipcode) : '');
        $neighborhoodValue = $formData['neighborhood'] ?? ($customer ? htmlspecialchars($customer->neighborhood) : '');
        $streetValue = $formData['street'] ?? ($customer ? htmlspecialchars($customer->street) : '');
        $cityValue = $formData['city'] ?? ($customer ? htmlspecialchars($customer->city) : '');
        $stateValue = $formData['state'] ?? ($customer ? htmlspecialchars($customer->state) : '');

        $statusOptions = [
            'active' => 'Ativo',
            'inactive' => 'Inativo',
            'blocked' => 'Bloqueado'
        ];

        $brazilianStates = [
            'AC' => 'Acre',
            'AL' => 'Alagoas',
            'AP' => 'Amapá',
            'AM' => 'Amazonas',
            'BA' => 'Bahia',
            'CE' => 'Ceará',
            'DF' => 'Distrito Federal',
            'ES' => 'Espírito Santo',
            'GO' => 'Goiás',
            'MA' => 'Maranhão',
            'MT' => 'Mato Grosso',
            'MS' => 'Mato Grosso do Sul',
            'MG' => 'Minas Gerais',
            'PA' => 'Pará',
            'PB' => 'Paraíba',
            'PR' => 'Paraná',
            'PE' => 'Pernambuco',
            'PI' => 'Piauí',
            'RJ' => 'Rio de Janeiro',
            'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul',
            'RO' => 'Rondônia',
            'RR' => 'Roraima',
            'SC' => 'Santa Catarina',
            'SP' => 'São Paulo',
            'SE' => 'Sergipe',
            'TO' => 'Tocantins'
        ];
?>
        <main>
            <h1><?= $title ?></h1>

            <div class="navigation-buttons">
                <a href="?page=customers"><i class="bi bi-arrow-left"></i> Voltar</a>
            </div>

            <section>
                <?php if ($message): ?>
                    <p class="message error">
                        <?= htmlspecialchars($message) ?>
                    </p>
                <?php endif; ?>

                <form method="POST" action="<?= $action ?>" class="customer-form">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $customer->id ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nome Completo:</label>
                            <input type="text" id="name" name="name"
                                value="<?= $nameValue ?>"
                                placeholder="Digite o nome completo"
                                class="<?= $fieldsWithErrors && in_array('name', $fieldsWithErrors) ? 'field-error' : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="cpf">CPF:</label>
                            <input type="text" id="cpf" name="cpf"
                                value="<?= $cpfValue ?>"
                                placeholder="000.000.000-00"
                                maxlength="14"
                                class="<?= $fieldsWithErrors && in_array('cpf', $fieldsWithErrors) ? 'field-error' : '' ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Telefone:</label>
                            <input type="text" id="phone" name="phone"
                                value="<?= $phoneValue ?>"
                                placeholder="(00) 00000-0000"
                                maxlength="15"
                                class="<?= $fieldsWithErrors && in_array('phone', $fieldsWithErrors) ? 'field-error' : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select id="status" name="status"
                                class="<?= $fieldsWithErrors && in_array('status', $fieldsWithErrors) ? 'field-error' : '' ?>">
                                <?php foreach ($statusOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= $statusValue === $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <h3>Endereço</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="zipcode">CEP:</label>
                            <input type="text" id="zipcode" name="zipcode"
                                value="<?= $zipcodeValue ?>"
                                placeholder="00000-000"
                                maxlength="9"
                                class="<?= $fieldsWithErrors && in_array('zipcode', $fieldsWithErrors) ? 'field-error' : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="neighborhood">Bairro:</label>
                            <input type="text" id="neighborhood" name="neighborhood"
                                value="<?= $neighborhoodValue ?>"
                                placeholder="Digite o bairro"
                                class="<?= $fieldsWithErrors && in_array('neighborhood', $fieldsWithErrors) ? 'field-error' : '' ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="street">Rua/Endereço:</label>
                        <input type="text" id="street" name="street"
                            value="<?= $streetValue ?>"
                            placeholder="Digite o endereço completo"
                            class="<?= $fieldsWithErrors && in_array('street', $fieldsWithErrors) ? 'field-error' : '' ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">Cidade:</label>
                            <input type="text" id="city" name="city"
                                value="<?= $cityValue ?>"
                                placeholder="Digite a cidade"
                                class="<?= $fieldsWithErrors && in_array('city', $fieldsWithErrors) ? 'field-error' : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="state">Estado:</label>
                            <select id="state" name="state"
                                class="<?= $fieldsWithErrors && in_array('state', $fieldsWithErrors) ? 'field-error' : '' ?>">
                                <option value="">Selecione o estado</option>
                                <?php foreach ($brazilianStates as $uf => $stateName): ?>
                                    <option value="<?= $uf ?>" <?= $stateValue === $uf ? 'selected' : '' ?>>
                                        <?= $stateName ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary"><?= $isEdit ? 'Atualizar' : 'Criar' ?> Cliente</button>
                </form>
            </section>
        </main>
    <?php
    }

    public static function renderList(array $customers, ?int $deleteId = null): void
    {
    ?>
        <main>
            <h1>Gerenciar Clientes</h1>

            <div class="navigation-buttons">
                <a href="?page=customers&action=create" class="btn-primary"><i class="bi bi-plus-lg"></i> Novo Cliente</a>
            </div>

            <?php if ($deleteId): ?>
                <div class="confirmation-dialog">
                    <p>Tem certeza que deseja excluir este cliente?</p>
                    <div class="confirmation-buttons">
                        <a href="?page=customers&action=delete&id=<?= $deleteId ?>" class="btn-danger">Sim, excluir</a>
                        <a href="?page=customers" class="btn-secondary">Cancelar</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($customers)): ?>
                <div class="customers-table-container">
                    <table class="customers-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Telefone</th>
                                <th>Status</th>
                                <th>Cidade</th>
                                <th>Estado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $customer): ?>
                                <tr class="customer-row">
                                    <td><?= $customer->id ?></td>
                                    <td class="customer-name"><?= htmlspecialchars($customer->name) ?></td>
                                    <td class="customer-cpf"><?= htmlspecialchars($customer->cpf) ?></td>
                                    <td><?= htmlspecialchars($customer->phone) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $customer->status ?>">
                                            <?php
                                            echo match ($customer->status) {
                                                'active' => 'Ativo',
                                                'inactive' => 'Inativo',
                                                'blocked' => 'Bloqueado',
                                                default => $customer->status
                                            };
                                            ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($customer->city) ?></td>
                                    <td><?= htmlspecialchars($customer->state) ?></td>
                                    <td class="actions">
                                        <a href="?page=customers&action=edit&edit=<?= $customer->id ?>" class="btn-edit">
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </a>
                                        <a href="?page=customers&confirm=<?= $customer->id ?>" class="btn-delete">
                                            <i class="bi bi-trash3"></i> Excluir
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">👥</div>
                    <h3>Nenhum cliente encontrado</h3>
                    <p>Comece criando seu primeiro cliente.</p>
                    <a href="?page=customers&action=create" class="btn-primary">Criar Primeiro Cliente</a>
                </div>
            <?php endif; ?>
        </main>
<?php
    }
}
