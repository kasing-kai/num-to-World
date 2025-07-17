<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convert Number To Word Calculator</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color:rgb(0, 0, 0);
            color: #1e293b;
            text-align: center;
            padding: 2rem;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2563eb;
        }
        form {
            margin: 1.5rem 0;
        }
        input[type="number"] {
            width: 80%;
            padding: 0.75rem;
            border: 2px solidrgb(81, 94, 110);
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        button {
            background:rgb(37, 235, 63);
            color: white;
            border: 1px solid;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1rem;
            margin: 0 5px;
        }
        button:hover {
            background: #1d4ed8;
        }
        .clear-button {
            background: #dc3545;
        }
        .clear-button:hover { 
            background: #c82333;
        }
        .result {
            margin-top: 1.5rem;
            padding: 1rem;
            background: #f1f5f9;
            border-radius: 0.5rem;
            text-align: left;
            white-space: pre-wrap;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .history-container {
            margin-top: 2rem;
            text-align: left;
            background: #f1f5f9;
            padding: 1rem;
            border-radius: 0.5rem;
            max-height: 300px;
            overflow-y: auto;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Convert Number To Word Calculator</h1>
        <form action="" method="POST">
            <label>Please input your data:</label>
            <input type="number" name="num" placeholder="Enter Number" required>
            <br><br>
            <button type="submit">Convert</button>
            <button type="button" onclick="viewHistory()">View History</button>
            <button type="button" onclick="clearHistory()" class="clear-button">Clear History</button> </form>

        <?php
        $filePath = "Number_list.txt";

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['num'])) {
            $num = intval($_POST['num']);
            $conversionOutput = ConvertNumbers($num, $filePath);
            echo "<div class='result'><h2>Converted Result</h2>" . htmlspecialchars($conversionOutput) . "</div>";
        }

        if (isset($_GET['clear_history']) && $_GET['clear_history'] == 'true') {
            if (file_exists($filePath)) {
                file_put_contents($filePath, '');
                echo "<div class='result'>History cleared successfully!</div>";
            } else {
                echo "<div class='result error'>No history file to clear.</div>";
            }
            echo "<script>window.location.href = window.location.pathname;</script>";
        }

        function numberToEnglishWords($number) {
            $words = [0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
                      5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
                      10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
                      14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen',
                      17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen',
                      20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty',
                      60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'];
            $levels = [1000000 => 'Million', 1000 => 'Thousand', 100 => 'Hundred'];

            if ($number < 20) {
                return $words[$number];
            } elseif ($number < 100) {
                return $words[10 * intval($number / 10)] . (($number % 10 > 0) ? '-' . $words[$number % 10] : '');
            } else {
                foreach ($levels as $value => $label) {
                    if ($number >= $value) {
                        $quotient = intval($number / $value);
                        $remainder = $number % $value;
                        $result = numberToEnglishWords($quotient) . ' ' . $label;
                        if ($remainder > 0) {
                            if ($value == 100 && $remainder < 100) {
                                $result .= ' and ';
                            } else {
                                $result .= ' ';
                            }
                            $result .= numberToEnglishWords($remainder);
                        }
                        return $result;
                    }
                }
            }
            return '';
        }

        function numberToKhmerWords($number) {
            if ($number == 0) return 'សូន្យ រៀល';

            $khmerDigits = ['សូន្យ', 'មួយ', 'ពីរ', 'បី', 'បួន', 'ប្រាំ', 'ប្រាំមួយ', 'ប្រាំពីរ', 'ប្រាំបី', 'ប្រាំបួន'];
            $khmerTens = ['', 'ដប់', 'ម្ភៃ', 'សាមសិប', 'សែសិប', 'ហាសិប', 'ហុកសិប', 'ចិតសិប', 'ប៉ែតសិប', 'កៅសិប'];
            $khmerUnits = ['', 'ពាន់', 'ម៉ឺន', 'សែន', 'លាន'];

            $parts = [];
            $position = 0;

            while ($number > 0) {
                $chunk = $number % 1000;
                if ($chunk > 0) {
                    $chunkWords = convertKhmerChunk($chunk, $khmerDigits, $khmerTens);
                    if ($position === 1) {
                        $chunkWords .= ' ' . $khmerUnits[1];
                    } elseif ($position === 2) {
                        $chunkWords .= ' ' . $khmerUnits[2];
                    } elseif ($position === 3) {
                         $chunkWords .= ' ' . $khmerUnits[3];
                    } elseif ($position >= 4) {
                        $chunkWords .= ' ' . $khmerUnits[4];
                    }
                    array_unshift($parts, trim($chunkWords));
                }
                $number = intval($number / 1000);
                $position++;
            }

            $finalKhmerWords = implode(' ', array_reverse($parts));
            return trim($finalKhmerWords) . ' រៀល';
        }

        function convertKhmerChunk($number, $khmerDigits, $khmerTens) {
            $words = '';

            $hundreds = intval($number / 100);
            $remainder = $number % 100;

            if ($hundreds > 0) {
                $words .= $khmerDigits[$hundreds] . ' រយ ';
            }

            if ($remainder > 0) {
                if ($remainder < 10) {
                    $words .= $khmerDigits[$remainder];
                } elseif ($remainder < 20) {
                    if ($remainder == 10) {
                        $words .= 'ដប់';
                    } else {
                        $words .= 'ដប់' . $khmerDigits[$remainder % 10];
                    }
                } else {
                    $tens = intval($remainder / 10);
                    $ones = $remainder % 10;
                    $words .= $khmerTens[$tens];
                    if ($ones > 0) {
                        $words .= $khmerDigits[$ones];
                    }
                }
            }

            return trim($words);
        }

        function NumKhmer($num, $currency = 'USD', $cent = true) {
            return round($num / 4000, 2) . " " . $currency;
        }

        function ConvertNumbers($num, $filePath) {
            $english = numberToEnglishWords($num);
            $khmer = numberToKhmerWords($num);
            $dollars = NumKhmer($num, '$', true);

            $output = "Number: $num\n";
            $output .= "English: $english Riel\n";
            $output .= "Khmer: $khmer\n";
            $output .= "USD: $dollars\n";
            $output .= "-------------------------\n";

            file_put_contents($filePath, $output, FILE_APPEND);

            return $output;
        }

        if (isset($_GET['view_history']) && $_GET['view_history'] == 'true') {
            echo "<div class='history-container'>";
            if (file_exists($filePath) && filesize($filePath) > 0) {
                echo "<h2>View History</h2>";
                echo "<pre>" . htmlspecialchars(file_get_contents($filePath)) . "</pre>";
            } else {
                echo "<h2>View History</h2>";
                echo "<p>No history available yet. Start by converting a number!</p>";
            }
            echo "</div>";
        }
        ?>
    </div>

    <script>
        function viewHistory() {
            window.location.href = window.location.pathname + '?view_history=true';
        }

        function clearHistory() {
            if (confirm('Are you sure you want to clear all history?')) {
                window.location.href = window.location.pathname + '?clear_history=true';
            }
        }
    </script>
</body>
</html>