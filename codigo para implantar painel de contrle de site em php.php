<?php
session_start();

$data_file = 'data.json';
// Garante que o data.json exista, ou cria
if (!file_exists($data_file)) {
    die("O arquivo de dados (data.json) não existe. Por favor, verifique se está no mesmo diretório.");
}
$data = json_decode(file_get_contents($data_file), true);

// Fazer Login
if (isset($_POST['login'])) {
    $user = trim(strtolower((string) $_POST['username']));
    $pass = trim((string) $_POST['password']);

    
    } else {
        $error = "Credenciais inválidas!";
    }
}

// Fazer Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Tela de Login se não estiver logado
if (!isset($_SESSION['admin_logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Administração</title>
        <link rel="icon" type="image/png" href="/img/LOGO.png" />
        <link rel="shortcut icon" href="/img/LOGO.png" type="image/x-icon" />
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                background: #0a0a0a;
                color: #fff;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                padding: 20px;
                box-sizing: border-box;
            }

            .login-box {
                background: #1a1a1a;
                padding: 40px 25px;
                border-radius: 12px;
                border: 1px solid #333;
                width: 100%;
                max-width: 400px;
                text-align: center;
                box-sizing: border-box;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            }

            .login-box h2 {
                color: #da1919;
                font-size: 24px;
                margin-top: 0;
                margin-bottom: 30px;
                letter-spacing: 1px;
            }

            input {
                width: 100%;
                padding: 16px;
                margin: 10px 0;
                border: none;
                border-radius: 8px;
                box-sizing: border-box;
                background: #2a2a2a;
                color: white;
                font-size: 16px;
                /* 16px evita zoom no iOS */
                outline: none;
            }

            input:focus {
                border: 1px solid #da1919;
                background: #333;
            }

            button {
                width: 100%;
                padding: 16px;
                background: #da1919;
                color: #fff;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-size: 16px;
                font-weight: bold;
                margin-top: 20px;
                text-transform: uppercase;
                letter-spacing: 1px;
                transition: background 0.3s;
            }

            button:hover {
                background: #b61515;
            }

            .password-container {
                position: relative;
                display: flex;
                align-items: center;
                width: 100%;
            }

            .toggle-btn {
                position: absolute;
                right: 15px;
                background: none;
                border: none;
                color: #aaa;
                cursor: pointer;
                padding: 0;
                margin: 0;
                box-shadow: none;
                width: auto;
                margin-top: 0;
                font-size: 20px;
            }

            .toggle-btn:hover {
                background: none;
                color: #fff;
            }

            .forgot-link {
                color: #aaa;
                font-size: 14px;
                margin-top: 25px;
                display: block;
                text-decoration: none;
                cursor: pointer;
            }

            .forgot-link:hover {
                color: #da1919;
                text-decoration: underline;
            }
        </style>
    </head>

    <body>
        <div class="login-box">
            <h2>Gestão do Site</h2>
            <?php if (isset($error))
                echo "<p style='color:#ff4444; font-size: 14px; margin-bottom: 20px;'>$error</p>"; ?>
            <form method="POST">
                <input type="email" name="username" placeholder="E-mail de acesso" required>
                <div class="password-container">
                    <input type="password" id="password_input" name="password" placeholder="Senha" required
                        style="padding-right: 50px;">
                    <button type="button" id="toggle_btn" class="toggle-btn" title="Mostrar/Ocultar">👁️</button>
                </div>
                <button type="submit" name="login">ENTRAR</button>
            </form>

            <a class="forgot-link" onclick="recuperarSenha()">Esqueceu a senha?</a>
        </div>

        <!-- Formulário Oculto de Recuperação via Formspree -->
        <form id="recovery_form" action="https://formspree.io/f/xdalbarz" method="POST" style="display: none;">
            <input type="hidden" name="Alerta" value="SOLICITAÇÃO DE RECUPERAÇÃO DE SENHA">
            <input type="hidden" name="Recado"
                
        </form>

        <script>
            // Lógica do Olho da senha
            const passInput = document.getElementById('password_input');
            const toggleBtn = document.getElementById('toggle_btn');
            toggleBtn.addEventListener('click', function () {
                if (passInput.type === 'password') {
                    passInput.type = 'text';
                    toggleBtn.innerHTML = '🙈';
                } else {
                    passInput.type = 'password';
                    toggleBtn.innerHTML = '👁️';
                }
            });

            // Lógica da Recuperação
            function recuperarSenha() {
                const conf = confirm("Deseja reenviar a senha ativa para o seu e-mail cadastrado ()?");
                if (conf) {
                    document.getElementById('recovery_form').submit();
                    alert("A senha foi enviada para o servidor de email. Cheque sua caixa de entrada em instantes.");
                }
            }
        </script>
    </body>

    </html>
    <?php
    exit;
}

// Processar a atualização (Form Submit)
if (isset($_POST['save'])) {
    $data['site_title'] = $_POST['site_title'];
    $data['showcase_title'] = $_POST['showcase_title'];
    $data['showcase_subtitle'] = $_POST['showcase_subtitle'];

    // Atualizar textos e nomes "Quem Somos"
    foreach ($_POST['about_text'] as $index => $text) {
        $data['about'][$index]['text'] = $text;
        $data['about'][$index]['name'] = $_POST['about_name'][$index];
    }

    // Excluir Destaques (se marcado)
    if (isset($_POST['delete_destaque']) && is_array($_POST['delete_destaque'])) {
        foreach ($_POST['delete_destaque'] as $index) {
            unset($data['destaque_images'][$index]);
        }
        $data['destaque_images'] = array_values($data['destaque_images']); // Reindexar
    }

    // Substituir Imagens - Destaques Existentes
    for ($i = 0; $i < count($data['destaque_images']); $i++) {
        if (isset($_FILES['destaque_img_' . $i]) && $_FILES['destaque_img_' . $i]['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['destaque_img_' . $i]['tmp_name'];
            $name = basename($_FILES['destaque_img_' . $i]['name']);
            $target = "img/" . time() . "_" . $name;
            if (move_uploaded_file($tmp_name, $target)) {
                $data['destaque_images'][$i] = $target;
            }
        }
    }

    // Novas Imagens - Destaques (Max 10)
    if (isset($_FILES['new_destaques']) && !empty($_FILES['new_destaques']['name'][0])) {
        foreach ($_FILES['new_destaques']['tmp_name'] as $key => $tmp) {
            if (count($data['destaque_images']) >= 10)
                break;
            if ($_FILES['new_destaques']['error'][$key] === UPLOAD_ERR_OK) {
                $name = basename($_FILES['new_destaques']['name'][$key]);
                $target = "img/" . time() . "_novo_" . $name;
                if (move_uploaded_file($tmp, $target)) {
                    $data['destaque_images'][] = $target;
                }
            }
        }
    }

    // Upload Novas Imagens - Quem Somos
    foreach ($data['about'] as $index => $person) {
        if (isset($_FILES['about_img_' . $index]) && $_FILES['about_img_' . $index]['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['about_img_' . $index]['tmp_name'];
            $name = basename($_FILES['about_img_' . $index]['name']);
            $target = "img/" . time() . "_" . $name;
            if (move_uploaded_file($tmp_name, $target)) {
                $data['about'][$index]['image'] = $target;
            }
        }
    }

    // Atualizar Textos e Imagens - Comodidades
    foreach ($_POST['como_title'] as $index => $title) {
        $data['comodidades'][$index]['title'] = $title;
        $data['comodidades'][$index]['text'] = $_POST['como_text'][$index];

        // Verifica se deseja apagar fotos antigas desta comodidade
        if (isset($_POST['delete_como_imgs'][$index])) {
            $data['comodidades'][$index]['images'] = "";
        }

        // Upload de Múltiplas Novas Fotos da Comodidade (Adiciona à string csv)
        if (isset($_FILES['new_como_imgs_' . $index]) && !empty($_FILES['new_como_imgs_' . $index]['name'][0])) {
            $new_images = [];
            foreach ($_FILES['new_como_imgs_' . $index]['tmp_name'] as $key => $tmp) {
                if ($_FILES['new_como_imgs_' . $index]['error'][$key] === UPLOAD_ERR_OK) {
                    $name = basename($_FILES['new_como_imgs_' . $index]['name'][$key]);
                    $target = "img/" . time() . "_como_" . $name;
                    if (move_uploaded_file($tmp, $target)) {
                        $new_images[] = $target;
                    }
                }
            }
            if (count($new_images) > 0) {
                $existing = trim($data['comodidades'][$index]['images']);
                if ($existing !== "") {
                    $data['comodidades'][$index]['images'] = $existing . ", " . implode(", ", $new_images);
                } else {
                    $data['comodidades'][$index]['images'] = implode(", ", $new_images);
                }
            }
        }
    }

    // Excluir Galeria (se marcado)
    if (isset($_POST['delete_galeria']) && is_array($_POST['delete_galeria'])) {
        foreach ($_POST['delete_galeria'] as $index) {
            unset($data['galeria'][$index]);
        }
        $data['galeria'] = array_values($data['galeria']); // Reindexar
    }

    // Substituir Imagens - Galeria
    for ($i = 0; $i < count($data['galeria']); $i++) {
        if (isset($_FILES['galeria_img_' . $i]) && $_FILES['galeria_img_' . $i]['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['galeria_img_' . $i]['tmp_name'];
            $name = basename($_FILES['galeria_img_' . $i]['name']);
            $target = "img/" . time() . "_" . $name;
            if (move_uploaded_file($tmp_name, $target)) {
                $data['galeria'][$i] = $target;
            }
        }
    }

    // Novas Imagens - Galeria (Max 50)
    if (isset($_FILES['new_galeria']) && !empty($_FILES['new_galeria']['name'][0])) {
        foreach ($_FILES['new_galeria']['tmp_name'] as $key => $tmp) {
            if (count($data['galeria']) >= 50)
                break;
            if ($_FILES['new_galeria']['error'][$key] === UPLOAD_ERR_OK) {
                $name = basename($_FILES['new_galeria']['name'][$key]);
                $target = "img/" . time() . "_galeria_" . $name;
                if (move_uploaded_file($tmp, $target)) {
                    $data['galeria'][] = $target;
                }
            }
        }
    }

    file_put_contents($data_file, json_encode($data, JSON_PRETTY_PRINT));
    $success = "Alterações salvas com sucesso! O site já foi atualizado automaticamente.";
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel CMS - Campos do Conde</title>
    <link rel="icon" type="image/png" href="/img/LOGO.png" />
    <link rel="shortcut icon" href="/img/LOGO.png" type="image/x-icon" />
    <style>
        body {
            font-family: sans-serif;
            background: #e5e5e5;
            color: #333;
            margin: 0;
            padding: 15px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        h1 {
            font-size: 1.6rem;
            margin: 0;
            color: #da1919;
        }

        h2 {
            color: #da1919;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            font-size: 1.3rem;
            margin-top: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            background: #fafafa;
        }

        input[type="file"] {
            width: 100%;
            box-sizing: border-box;
        }

        .img-preview {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
            border: 2px solid #ddd;
        }

        .btn {
            padding: 15px 20px;
            background: #da1919;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            box-sizing: border-box;
        }

        .btn:hover {
            background: #111;
        }

        .header {
            display: flex;
            flex-direction: column;
            gap: 15px;
            text-align: center;
            border-bottom: 2px solid #da1919;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .alert {
            padding: 15px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        .card {
            border: 1px solid #eee;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            background: #fdfdfd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }

        .flex-card {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .delete-box {
            margin-top: 10px;
            color: #ff4444;
            font-size: 14px;
            font-weight: bold;
        }

        /* Desktop adjustments */
        @media (min-width: 600px) {
            .header {
                flex-direction: row;
                justify-content: space-between;
                text-align: left;
            }

            .flex-card {
                flex-direction: row;
                align-items: flex-start;
            }

            .flex-card>div:first-child {
                flex: 0 0 150px;
            }

            .flex-card>div:last-child {
                flex: 1;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Painel de Controle</h1>
                <small style="color: #666;">Campos do Conde RC</small>
            </div>
            <div>
                <a href="index.php" target="_blank"
                    style="color: #da1919; text-decoration: none; margin-right: 15px; font-weight: bold;">Ver Site</a>
                <a href="admin.php?logout=1"
                    style="color: #111; text-decoration: none; padding: 8px 15px; background: #eee; border-radius: 4px;">Sair
                    / Logout</a>
            </div>
        </div>

        <?php if (isset($success))
            echo "<div class='alert'>$success</div>"; ?>

        <form method="POST" enctype="multipart/form-data">

            <div
                style="position: sticky; top: 0; background: rgba(255,255,255,0.9); padding: 10px 0; z-index: 100; border-bottom: 1px solid #ccc; margin-bottom: 20px;">
                <button type="submit" name="save" class="btn">☁️ SALVAR TODAS AS ALTERAÇÕES NAS NUVENS</button>
            </div>

            <h2>1. Textos e Títulos Principais</h2>
            <div class="form-group">
                <label>Título Aba do Navegador (Página)</label>
                <input type="text" name="site_title" value="<?php echo htmlspecialchars($data['site_title']); ?>">
            </div>
            <div class="form-group">
                <label>Título Principal (Banner Topo)</label>
                <input type="text" name="showcase_title"
                    value="<?php echo htmlspecialchars($data['showcase_title']); ?>">
            </div>
            <div class="form-group">
                <label>Subtítulo (Abaixo do banner principal)</label>
                <input type="text" name="showcase_subtitle"
                    value="<?php echo htmlspecialchars($data['showcase_subtitle']); ?>">
            </div>

            <h2>2. Nossos Destaques (Máx 10 fotos)</h2>
            <div
                style="margin-bottom: 20px; padding: 15px; background: #e3f2fd; border-radius: 6px; border: 1px solid #90caf9;">
                <label style="color: #0d47a1;">➕ Adicionar Novas Fotos de Destaque</label>
                <input type="file" name="new_destaques[]" multiple accept="image/*" style="margin-top: 10px;">
                <small style="color: #0d47a1; display: block; margin-top: 5px;">Você pode selecionar várias fotos
                    segurando o dedo ou apertando Ctrl. As fotos serão adaptadas ao estilo do site
                    automaticamente.</small>
            </div>
            <div class="grid-cards">
                <?php foreach ($data['destaque_images'] as $i => $img): ?>
                    <div class="card" style="text-align: center; padding: 10px;">
                        <img src="<?php echo $img; ?>" class="img-preview" alt="Preview">
                        <label style="font-size: 13px;">Substituir Foto <?php echo $i + 1; ?></label>
                        <input type="file" name="destaque_img_<?php echo $i; ?>" accept="image/*"
                            style="font-size: 12px; margin-top: 5px;">
                        <div class="delete-box">
                            <label><input type="checkbox" name="delete_destaque[]" value="<?php echo $i; ?>"> 🗑️ Excluir
                                Foto</label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <h2>3. Equipe / Quem Somos</h2>
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <?php foreach ($data['about'] as $index => $person): ?>
                    <div class="card flex-card">
                        <div style="text-align: center;">
                            <img src="<?php echo $person['image']; ?>" class="img-preview"
                                style="height: 120px; width: 120px; border-radius: 50%;">
                            <label style="font-size: 13px; margin-top: 5px;">Nova Foto</label>
                            <input type="file" name="about_img_<?php echo $index; ?>" accept="image/*"
                                style="font-size: 12px;">
                        </div>
                        <div>
                            <label>Nome do Colaborador</label>
                            <input type="text" name="about_name[<?php echo $index; ?>]"
                                value="<?php echo htmlspecialchars($person['name']); ?>" style="margin-bottom: 15px;">
                            <label>Depoimento / Relato</label>
                            <textarea rows="4"
                                name="about_text[<?php echo $index; ?>]"><?php echo htmlspecialchars($person['text']); ?></textarea>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <h2>4. Comodidades</h2>
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <?php foreach ($data['comodidades'] as $index => $comodidade): ?>
                    <div class="card flex-card">
                        <div>
                            <div style="text-align: center; margin-bottom: 15px;">
                                <i class="<?php echo htmlspecialchars($comodidade['icon']); ?> fa-3x"
                                    style="color: #da1919;"></i>
                            </div>
                            <label style="font-size: 13px; color: #0d47a1; font-weight: bold;">➕ Adicionar Fotos ao
                                Banner</label>
                            <input type="file" name="new_como_imgs_<?php echo $index; ?>[]" multiple accept="image/*"
                                style="font-size: 12px; margin-top: 5px;">

                            <div class="delete-box" style="margin-top: 15px;">
                                <label style="font-size: 12px;"><input type="checkbox"
                                        name="delete_como_imgs[<?php echo $index; ?>]" value="1"> 🗑️ Apagar TODAS as fotos
                                    desta comodidade</label>
                            </div>
                        </div>
                        <div>
                            <label>Título</label>
                            <input type="text" name="como_title[<?php echo $index; ?>]"
                                value="<?php echo htmlspecialchars($comodidade['title']); ?>" style="margin-bottom: 15px;">
                            <label>Descrição</label>
                            <textarea rows="3"
                                name="como_text[<?php echo $index; ?>]"><?php echo htmlspecialchars($comodidade['text']); ?></textarea>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <h2>5. Galeria de Fotos (Ate 50 fotos)</h2>
            <div
                style="margin-bottom: 20px; padding: 15px; background: #e3f2fd; border-radius: 6px; border: 1px solid #90caf9;">
                <label style="color: #0d47a1;">➕ Adicionar Novas Fotos à Galeria</label>
                <input type="file" name="new_galeria[]" multiple accept="image/*" style="margin-top: 10px;">
                <small style="color: #0d47a1; display: block; margin-top: 5px;">A quantidade limite para não pesar o
                    site são 50 fotos gerais. A index já mostrará as últimas sozinhas!</small>
            </div>
            <div class="grid-cards">
                <?php foreach ($data['galeria'] as $i => $img): ?>
                    <div class="card" style="text-align: center; padding: 10px;">
                        <img src="<?php echo $img; ?>" class="img-preview" alt="Preview">
                        <label style="font-size: 13px;">Substituir Foto <?php echo $i + 1; ?></label>
                        <input type="file" name="galeria_img_<?php echo $i; ?>" accept="image/*"
                            style="font-size: 12px; margin-top: 5px;">
                        <div class="delete-box">
                            <label><input type="checkbox" name="delete_galeria[]" value="<?php echo $i; ?>"> 🗑️ Excluir
                                Foto</label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top: 40px; margin-bottom: 40px;">
                <button type="submit" name="save" class="btn" style="padding: 20px; font-size: 18px;">☁️ SALVAR TODAS AS
                    ALTERAÇÕES NAS NUVENS</button>
            </div>
        </form>
    </div>
</body>

</html>