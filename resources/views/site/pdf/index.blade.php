<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-after: always;
        }

        .box {
            border: 1px solid black !important
        }

        body {
            margin: 80px 80px;
            /* font-size: 20px !important; */

        }

        table {

            border-collapse: collapse;
        }

        .services,
        .service-th,
        .services td {

            border: 1px solid black;
            padding: 5px
        }

        .services td {
            text-align: center
        }

        .tax-rates td {
            /* border: 1px solid black; */

            padding: 2px 20px;
        }

        .invoice-in-words td {
            border: 1px solid black;
            padding: 10px;

        }

        .table-borderless td {
            padding: 8px 10px;
        }
    </style>
</head>

<body>
    <div style="text-align: center; margin-bottom: 20px">
        <h2>TAX INVOICE</h2>
        <p>Invoice issued by Ancient Helpro Private Limited on behalf of:</p>
    </div>

    <div style="margin-bottom: 20px">
        <img width="80px"
            src="data:image/jpg;base64, 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"
            alt="">
        <h4>Ancient Helpro Private Limited</h4>
        <p>Plot No.R-488, Sector 8,</p>
        <p>MIDC Industrial Area,</p>
        <p>Rabale, Navi Mumbai,</p>
        <p>Maharashtra 400701</p>
        <p>GSTIN: 27ABBCA3087A1Z2</p>
    </div>


    {{-- <div style="padding: 0px 120px;">
        <p style="float:left"><b>Invoice To: </b>  Sanny Deuji</p>
        <p style="float: right"><b>Invoice No: </b>  123456</p>
    </div>

    <div style="padding: 40px 120px;">
        <p style="float:left"><b>Customers Address: </b>  Plot NO R-488 Sector 8, Rabale,

            Navi Mumbai</p>
        <p style="float: right"><b>Invoice No: </b>  123456</p>
    </div> --}}
    <div style="text-align:center; margin-bottom: 20px">
        <table class="table table-borderless">
            <tbody>
                <tr>
                    <td style="width: 50%;">
                        <div class="table-responsive">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="width: 50%"><strong>Invoice To: </strong></th>
                                        <td>
                                            {{ $booking['user']['name'] }}
                                        </td>

                                    </tr>
                                    <tr>
                                        <th scope="row"><strong>Customer Address: </strong></th>
                                        <td>
                                            {{ $booking['checkout']['address']['address'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><strong>Contact Number:</strong></th>
                                        <td>
                                            {{ $booking['user']['phone'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><strong>Email ID:</strong></th>
                                        <td>
                                            {{ $booking['user']['email'] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                    <td style="width: 50%; padding:0%;">
                        <div class="table-responsive">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="width: 50%"><strong>Invoice No:</strong></th>
                                        <td>
                                            123456
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><strong>Invoice Date:</strong></th>
                                        <td>
                                            07/06/2024
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><strong></strong></th>
                                        <td>
                                            {{-- Doha --}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><strong></strong></th>
                                        <td>
                                            {{-- (+974) 4444 4444 --}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><strong></strong></th>
                                        <td>
                                            {{-- info@example.com --}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- service description --}}
    <div style="margin-bottom: 20px">
        <p><b>Description of Services</b></p>
        <table class="services">
            <thead>
                <th class="service-th">Category</th>
                <th class="service-th">Total Labour Book</th>
                <th class="service-th">Total Hour</th>
                <th class="service-th">Per Hour Price</th>
                <th class="service-th">Amount (Rs.)</th>
                <th class="service-th">Discount</th>
                <th class="service-th">Net Assessable Value(Rs.)</th>
            </thead>
            <tbody>
                <tr>
                    <td>{{$booking['checkout']['category']['title']}}</td>
                    <td>{{$booking['checkout']['labour_quantity']}}</td>
                    <td>800.00</td>
                    <td>{{$booking['total_amount']}}</td>
                    <td>{{$booking['total_amount']}}</td>
                    <td>00</td>
                    <td>{{$booking['total_amount']}}</td>
                </tr>
                <tr>
                    <td><b>Subtotal</b></td>
                    <td><b>-</b></td>
                    <td><b>-</b></td>
                    <td>{{$booking['total_amount']}}</td>
                    <td>{{$booking['total_amount']}}</td>
                    <td><b>00</b></td>
                    <td>{{$booking['total_amount']}}</td>
                </tr>
                <tr>
                    <td>Service/Charges</td>
                    <td>{{$booking['checkout']['labour_quantity']}}</td>
                    <td>9</td>
                    <td>-</td>
                    <td>{{$booking['service_charges']}}</td>
                    <td>00</td>
                    <td>{{$booking['service_charges']}}</td>
                </tr>
                <tr>
                    <td><b>Subtotal With Service Charges</b></td>
                    <td><b>-</b></td>
                    <td><b>-</b></td>
                    <td><b>0.00</b></td>
                    <td><b>{{(float)$booking['service_charges'] + (float)$booking['total_amount']}}</b></td>
                    <td><b>00</b></td>
                    <td><b>{{(float)$booking['service_charges'] + (float)$booking['total_amount']}}</b></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-bottom: 20px;margin-left: 350px">

        <table class="tax-rates">
            <thead>
                <th style="text-align: start">Taxes</th>
                <th>Rates</th>
                <th></th>
            </thead>
            <tbody>
                <tr>
                    <td>IGST</td>
                    <td>0%</td>
                    <td>0.00</td>
                </tr>
                <tr>
                    <td>CGST</td>
                    <td>2.5%</td>
                    <td>21.43</td>
                </tr>
                <tr>
                    <td>SGST/UTGST</td>
                    <td>2.5%</td>
                    <td>21.43</td>
                </tr>
                <tr>
                    <td>Service Charges</td>
                    <td></td>
                    <td>{{$booking['service_charges']}}</td>
                </tr>
                <tr>
                    <td><b>Invoice Total</b></td>
                    <td></td>
                    <td>{{(float)$booking['service_charges'] + (float)$booking['total_amount']}}</td>
                </tr>
            </tbody>
        </table>
    </div>


    {{-- invoice total in words --}}
    <div>
        <table class="invoice-in-words">
            <tbody>
                <tr>
                    <td width="50%">Invoice total in words</td>
                    <td width="50%"><b>{{ucwords($total_amount_in_words)}}</b></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
