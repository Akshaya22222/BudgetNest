<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings - BudgetNest</title>
  <style>
    /* Background */
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #accff1ff 0%, #f3c5c5ff 100%);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
    }

    /* Glass container */
    .container {
      background: rgba(255, 255, 255, 0.7);
      backdrop-filter: blur(12px);
      border-radius: 15px;
      padding: 40px;
      width: 100%;
      max-width: 500px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      animation: fadeIn 0.8s ease-in-out;
    }

    h2 {
      color: #2c3e50;
      text-align: center;
      font-size: 28px;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin: 15px 0 8px;
      font-weight: 600;
      color: #34495e;
    }

    input, select {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      border: 1px solid #ccc;
      outline: none;
      font-size: 15px;
      transition: all 0.3s ease;
      background: rgba(255,255,255,0.9);
    }

    input:focus, select:focus {
      border-color: #2c3e50;
      box-shadow: 0 0 8px rgba(44,62,80,0.2);
      transform: scale(1.02);
    }

    button {
      margin-top: 25px;
      width: 100%;
      padding: 12px;
      background: linear-gradient(135deg, #2c3e50, #34495e);
      color: white;
      font-size: 16px;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    button:hover {
      background: linear-gradient(135deg, #1a252f, #2c3e50);
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(0,0,0,0.2);
    }

    .back {
      text-decoration: none;
      display: block;
      text-align: center;
      margin-top: 18px;
      font-size: 15px;
      color: #2c3e50;
      transition: color 0.3s ease;
    }

    .back:hover {
      color: #000;
    }

    /* Fade animation */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>⚙️ Settings</h2>
    <form>
      <label>Change Email</label>
      <input type="email" placeholder="Enter new email">

      <label>Change Password</label>
      <input type="password" placeholder="Enter new password">

      <label>Notification Preferences</label>
      <select>
        <option>Email Alerts</option>
        <option>SMS Alerts</option>
        <option>Push Notifications</option>
      </select>

      <button type="submit">Save Changes</button>
    </form>
    <a href="dashboard.php" class="back">⬅ Back to Dashboard</a>
  </div>
</body>
</html>
