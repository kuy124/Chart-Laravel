<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chart Kelahiran dan Kematian di Jakarta Timur tahun 2020</title>
    <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/512/404/404621.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            fetch('/chart-data')
                .then(response => response.json())
                .then(chartData => {
                    const dataArray = [
                        ['Bulan', 'Kelahiran', 'Kematian']
                    ];
                    chartData.forEach(item => {
                        dataArray.push([item.year, item.sales, item.expenses]);
                    });

                    const data = google.visualization.arrayToDataTable(dataArray);

                    const options = {
                        title: 'Kelahiran dan Kematian di Jakarta Timur tahun 2020 perbulan',
                        legend: {
                            position: 'top'
                        },
                        width: '100%',
                        height: '100%'
                    };

                    const chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
                    chart.draw(data, options);

                    window.addEventListener('resize', function() {
                        chart.draw(data, options);
                    });
                });
        }

        function fetchTableData() {
            fetch('/chart-data')
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('dataTableBody');
                    tableBody.innerHTML = '';

                    data.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                    <td>${item.id}</td> 
                    <td>${item.year}</td>
                    <td>${item.sales}</td>
                    <td>${item.expenses}</td>
                `;
                        tableBody.appendChild(row);
                    });
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            fetchTableData();

            $('#addDataModal').on('hidden.bs.modal', function() {
                location.reload();
            });

            document.getElementById('saveData').addEventListener('click', function() {
                const id = this.dataset.id || '';
                const year = document.getElementById('year').value;
                const sales = document.getElementById('sales').value;
                const expenses = document.getElementById('expenses').value;

                if (year && sales && expenses) {
                    const url = id ? '/update-chart-data' :
                        '/add-chart-data';
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                id,
                                year,
                                sales,
                                expenses
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            $('#addDataModal').modal('hide');
                            location.reload();
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        });
    </script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .container {
            margin-top: 30px;
        }

        #curve_chart {
            margin-bottom: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            text-align: center;
            vertical-align: middle;
        }

        thead {
            background-color: #007bff;
            color: #fff;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .btn-primary,
        .btn-warning,
        .btn-danger {
            border-radius: 20px;
            padding: 5px 15px;
            transition: background-color 0.3s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .modal-header,
        .modal-footer {
            background-color: #f8f9fa;
        }

        .modal-content {
            border-radius: 8px;
        }

        .modal-header h5 {
            color: #007bff;
        }
    </style>
</head>

<body>
    <p align="center"><a href="{{ url('/2') }}"><button class="btn-success btn mt-2">Chart kedua</button></a></p>
    <div class="container mt-5">
        <a href="{{ url('login') }}" class="btn btn-warning mb-3">Masuk</a>
        <a href="{{ url('kontak') }}">
            <button type="button" class="btn btn-danger mb-3">
                Kontak
            </button>
        </a>
        <div id="curve_chart" class="chart" style="width: 100%; height: 500px"></div>
        <p align="center">Sumber/Source: Dinas Kependudukan dan Catatan Sipil Provinsi DKI Jakarta/Civil Registration
            Service of DKI Jakarta Province</p>
        <div class="text-center mt-4">
            <button class="btn btn-success" id="downloadChartBtn">Simpan statistik sebagai PDF</button>
        </div>
        <div class="pdf">
            <div class="table-responsive mt-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Bulan</th>
                            <th>Kelahiran</th>
                            <th>Kematian</th>
                        </tr>
                    </thead>
                    <tbody id="dataTableBody">

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <button class="btn btn-success" id="downloadTableBtn">Simpan tabel sebagai PDF</button>
    </div>

    <footer class="bg-light text-center text-lg-start mt-4">
        <div class="container p-4">
            <p>&copy; 2024 Jakarta Timur</p>
            <p>Didukung oleh <a href="https://developers.google.com/chart" target="_blank">Google Charts</a></p>
        </div>
    </footer>

    <script>
        document.getElementById('downloadChartBtn').addEventListener('click', function() {
            var element = document.querySelector('.chart');
            html2pdf().from(element).set({
                margin: 0.2,
                filename: 'Chart_Kelahiran_Kematian_Jakarta_Timur_2020.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    width: 1100
                },
                jsPDF: {
                    orientation: 'landscape',
                    unit: 'in',
                    format: 'letter',
                    compressPDF: true
                }
            }).save();
        });

        document.getElementById('downloadTableBtn').addEventListener('click', function() {
            var element = document.querySelector('.pdf');
            html2pdf().from(element).set({
                margin: 0.2,
                filename: 'Tabel_Kelahiran_Kematian_Jakarta_Timur_2020.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                },
                jsPDF: {
                    orientation: 'portrait',
                    unit: 'in',
                    format: 'letter',
                    compressPDF: true
                }
            }).save();
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
