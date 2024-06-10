<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Template</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        header {
            background-color: #333;
            color: white;
            padding: 1rem;
        }

        nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            gap: 1rem;
        }

        nav ul li {
            display: inline;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
        }

        main {
            display: flex;
            flex: 1;
        }

        aside {
            background-color: #f4f4f4;
            padding: 1rem;
            width: 200px;
        }

        aside ul {
            list-style: none;
            padding: 0;
        }

        aside ul li {
            margin: 1rem 0;
        }

        aside ul li a {
            text-decoration: none;
            color: #333;
        }

        section {
            flex: 1;
            padding: 1rem;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1rem;
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <h1>Dashboard</h1>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <aside>
            <ul>
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Profile</a></li>
                <li><a href="#">Settings</a></li>
            </ul>
        </aside>
        <section>
            <h2>Welcome to the Dashboard</h2>
            <p>This is your main content area.</p>
            <?php
            // PHP 코드를 사용하여 동적으로 데이터를 표시합니다.
            $data = array(
                "Item 1" => "Description for item 1",
                "Item 2" => "Description for item 2",
                "Item 3" => "Description for item 3"
            );

            foreach ($data as $item => $description) {
                echo "<h3>$item</h3>";
                echo "<p>$description</p>";
            }
            ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Dashboard Template</p>
    </footer>
</body>

</html>