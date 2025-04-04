<?php
// 配置部分
$filename = 'todos.txt';
date_default_timezone_set('Asia/Shanghai');

// 读取数据
$todos = file_exists($filename) ? unserialize(file_get_contents($filename)) : [];

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['task'])) {
        $newTask = [
            'task' => htmlspecialchars($_POST['task']),
            'completed' => false,
            'time' => time()
        ];
        array_unshift($todos, $newTask); // 新任务添加到开头
        file_put_contents($filename, serialize($todos));
    }
    header('Location: '.$_SERVER['PHP_SELF']);
    exit;
}

// 处理操作请求
if (isset($_GET['action']) && isset($_GET['index'])) {
    $index = (int)$_GET['index'];
    $validActions = ['complete', 'delete'];

    if (isset($todos[$index]) && in_array($_GET['action'], $validActions)) {
        switch ($_GET['action']) {
            case 'complete':
                $todos[$index]['completed'] = !$todos[$index]['completed'];
                break;
            case 'delete':
                array_splice($todos, $index, 1);
                break;
        }
        file_put_contents($filename, serialize($todos));
    }
    header('Location: '.$_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>待办清单</title>
    <style>
        :root {
            --primary: #2196F3;
            --secondary: #1976D2;
            --background: #E3F2FD;
            --text: #333;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
            background: var(--background);
            color: var(--text);
        }

        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
        }

        h1 {
            color: var(--secondary);
            text-align: center;
            margin: 0 0 2rem;
            font-size: 2.2rem;
        }

        .add-form {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        input[type="text"] {
            flex: 1;
            padding: 0.8rem;
            border: 2px solid #BBDEFB;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: var(--primary);
        }

        button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s, transform 0.1s;
        }

        button:hover {
            background: var(--secondary);
            transform: translateY(-1px);
        }

        button:active {
            transform: translateY(0);
        }

        .todo-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .todo-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            margin: 0.5rem 0;
            background: #E1F5FE;
            border-radius: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .todo-item:hover {
            transform: translateX(5px);
            box-shadow: 2px 2px 6px rgba(0,0,0,0.1);
        }

        .todo-text {
            flex: 1;
            margin: 0 1rem;
            word-break: break-word;
        }

        .completed .todo-text {
            text-decoration: line-through;
            opacity: 0.7;
        }

        .time {
            font-size: 0.9rem;
            color: #666;
            min-width: 80px;
            text-align: right;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
            margin-left: 1rem;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .complete-btn {
            background: #4CAF50;
            color: white;
        }

        .delete-btn {
            background: #F44336;
            color: white;
        }

        @media (max-width: 480px) {
            .todo-item {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            .time {
                order: -1;
                width: 100%;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 待办清单</h1>
        
        <form class="add-form" method="post">
            <input type="text" name="task" placeholder="输入新任务..." required>
            <button type="submit">➕ 添加</button>
        </form>

        <ul class="todo-list">
            <?php foreach ($todos as $index => $todo): ?>
            <li class="todo-item <?= $todo['completed'] ? 'completed' : '' ?>">
                <span class="todo-text"><?= $todo['task'] ?></span>
                <span class="time"><?= date('Y/m/d H:i', $todo['time']) ?></span>
                <div class="actions">
                    <a href="?action=complete&index=<?= $index ?>" class="action-btn complete-btn">
                        <?= $todo['completed'] ? '↩️ 撤销' : '✅ 完成' ?>
                    </a>
                    <a href="?action=delete&index=<?= $index ?>" class="action-btn delete-btn">🗑️ 删除</a>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>