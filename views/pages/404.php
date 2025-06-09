<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página Não Encontrada | Pizza Universe</title>
    <link rel="stylesheet" href="./assets/stylesheets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .error-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #f4f3f2 0%, #fffcfb 100%);
        }

        .error-content {
            text-align: center;
            max-width: 600px;
            background: #ffffff;
            padding: 60px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .error-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #ffb400, #ffd700, #ffb400);
            animation: shimmer 3s ease-in-out infinite;
        }

        .error-number {
            font-size: 8rem;
            font-weight: 800;
            color: #ffb400;
            text-shadow: 4px 4px 8px rgba(255, 180, 0, 0.3);
            margin: 0;
            line-height: 1;
            animation: bounce 2s ease-in-out infinite;
        }

        .pizza-emoji {
            font-size: 3rem;
            display: inline-block;
            animation: rotate 4s linear infinite;
            margin: 0 10px;
        }

        .error-title {
            font-size: 2.5rem;
            color: #2c1b1b;
            margin: 20px 0;
            font-weight: 700;
        }

        .error-message {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .error-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 2px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ffb400, #ffd700);
            color: #2c1b1b;
            box-shadow: 0 4px 15px rgba(255, 180, 0, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2c1b1b, #444);
            color: #ffb400;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(44, 27, 27, 0.3);
        }

        .btn-secondary {
            background: transparent;
            color: #2c1b1b;
            border-color: #2c1b1b;
        }

        .btn-secondary:hover {
            background: #2c1b1b;
            color: #ffffff;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(44, 27, 27, 0.2);
        }

        .suggestions {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #f0f0f0;
        }

        .suggestions h3 {
            color: #2c1b1b;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .suggestions-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .suggestions-list li {
            background: rgba(255, 180, 0, 0.1);
            padding: 15px;
            border-radius: 12px;
            transition: all 0.3s ease;
            border-left: 4px solid #ffb400;
        }

        .suggestions-list li:hover {
            background: rgba(255, 180, 0, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 180, 0, 0.2);
        }

        .suggestions-list a {
            color: #2c1b1b;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .suggestions-list a:hover {
            color: #ffb400;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-20px);
            }

            60% {
                transform: translateY(-10px);
            }
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes shimmer {

            0%,
            100% {
                opacity: 0.8;
            }

            50% {
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .error-content {
                padding: 40px 30px;
                margin: 20px;
            }

            .error-number {
                font-size: 6rem;
            }

            .error-title {
                font-size: 2rem;
            }

            .error-message {
                font-size: 1.1rem;
            }

            .error-actions {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .suggestions-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include_once './views/components/header.php'; ?>

    <div class="error-container">
        <div class="error-content">
            <h1 class="error-number">4<span class="pizza-emoji">🍕</span>4</h1>

            <h2 class="error-title">Ops! Página Não Encontrada</h2>

            <p class="error-message">
                Parece que essa página saiu para entregar pizza em outro universo! 🚀<br>
                Não se preocupe, temos muitas outras delícias esperando por você.
            </p>

            <div class="error-actions">
                <a href="?page=home" class="btn btn-primary">
                    <i class="bi bi-house-fill"></i>
                    Voltar ao Início
                </a>
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i>
                    Página Anterior
                </a>
            </div>

            <div class="suggestions">
                <h3>Que tal explorar essas opções?</h3>
                <ul class="suggestions-list">
                    <li>
                        <a href="?page=pizzas">
                            <i class="bi bi-pizza"></i>
                            Ver Cardápio de Pizzas
                        </a>
                    </li>
                    <li>
                        <a href="?page=orders">
                            <i class="bi bi-bag-check"></i>
                            Meus Pedidos
                        </a>
                    </li>
                    <li>
                        <a href="?page=dashboard">
                            <i class="bi bi-speedometer2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="?page=customers">
                            <i class="bi bi-people"></i>
                            Clientes
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <?php include_once './views/components/footer.php'; ?>
</body>

</html>