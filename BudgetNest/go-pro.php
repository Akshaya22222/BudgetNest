<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Go Pro - BudgetNest</title>
  <style>
    /* Background */
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #fdfcfb, #e2d1c3);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
    }

    /* Card container */
    .card {
      background: #fff;
      max-width: 520px;
      width: 90%;
      padding: 35px 25px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      text-align: center;
      animation: slideUp 0.8s ease-in-out;
      position: relative;
      overflow: hidden;
    }

    /* Decorative glowing border */
    .card::before {
      content: "";
      position: absolute;
      top: -2px;
      left: -2px;
      right: -2px;
      bottom: -2px;
      border-radius: 18px;
      background: linear-gradient(135deg, #e67e22, #f39c12, #f1c40f);
      z-index: -1;
      filter: blur(10px);
      opacity: 0.4;
    }

    h2 {
      color: #e67e22;
      font-size: 28px;
      margin-bottom: 10px;
    }

    p {
      font-size: 16px;
      color: #2c3e50;
      margin-bottom: 20px;
    }

    ul {
      text-align: left;
      padding: 0;
      margin: 0 auto 20px auto;
      list-style: none;
      max-width: 350px;
    }

    li {
      margin: 12px 0;
      font-size: 16px;
      padding-left: 30px;
      position: relative;
      color: #34495e;
    }

    li::before {
      content: "✨";
      position: absolute;
      left: 0;
      color: #e67e22;
      font-size: 18px;
    }

    /* Upgrade Button */
    .btn {
      display: inline-block;
      background: linear-gradient(135deg, #e67e22, #d35400);
      color: white;
      padding: 14px 35px;
      border-radius: 8px;
      font-size: 17px;
      font-weight: bold;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }

    .btn:hover {
      background: linear-gradient(135deg, #d35400, #c0392b);
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.25);
    }

    /* Back link */
    .back {
      display: block;
      margin-top: 20px;
      text-decoration: none;
      font-size: 15px;
      color: #34495e;
      transition: 0.3s;
    }

    .back:hover {
      color: #000;
    }

    /* Animation */
    @keyframes slideUp {
      from { transform: translateY(30px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>🌟 Upgrade to BudgetNest Pro</h2>
    <p>Unlock premium features:</p>
    <ul>
      <li>Advanced Analytics</li>
      <li>Unlimited Expense Categories</li>
      <li>Smart Financial Insights</li>
      <li>Priority Support</li>
    </ul>
    <a href="#" class="btn">🚀 Upgrade Now</a>
    <a href="dashboard.php" class="back">⬅ Back to Dashboard</a>
  </div>
</body>
</html>
