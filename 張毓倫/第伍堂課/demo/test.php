<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>收到的資料 - 伺服器端</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 50px;
            background: #f9f9f9;
            min-height: 100vh;
            margin: 0;
        }

        .result-box {
            background: white;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        h1 {
            color: #2e7d32;
            margin-top: 0;
        }

        ul {
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }

        li {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        li:last-child {
            border-bottom: none;
        }

        strong {
            color: #555;
            width: 120px;
            display: inline-block;
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .note {
            color: #dc3545;
            font-size: 13px;
            margin-top: 40px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="result-box">
        <h1>✅ 這裡是 test.php (伺服器端)</h1>
        <form action="traditional_form.php" method="POST"> <!--POST GET-->
            <input type="text" name="name" placeholder="姓名">
            <input type="email" name="email" placeholder="Email">
            <button type="submit">送出</button>
        </form>
    </div>

</body>

</html>