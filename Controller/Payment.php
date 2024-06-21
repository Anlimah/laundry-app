<?php

namespace Controller;

use Core\Database;

class Payment
{
    private $db;

    public function __construct($db_config)
    {
        $this->db = new Database($db_config);
    }

    public function getInvoices($laundryId)
    {
        return $this->db->run("SELECT * FROM Invoices WHERE laundry_id = ?", [$laundryId])->fetchAll();
    }

    public function getInvoice($id)
    {
        return $this->db->run("SELECT * FROM Invoices WHERE id = ?", [$id])->fetchOne();
    }

    public function payInvoice($invoice_id, $amount, $payment_method)
    {
        // Assume the payment process is done and we get a response status
        $paymentStatus = 'Success'; // This should come from the payment gateway

        $result = $this->db->run(
            'INSERT INTO InvoicePayments (invoice_id, amount, payment_method, status) VALUES (?, ?, ?, ?)',
            [$invoice_id, $amount, $payment_method, $paymentStatus]
        )->insert();
        if ($result) {
            $updated = $this->db->run(
                'UPDATE Invoices SET status = ? WHERE id = ?',
                [$paymentStatus == 'Success' ? 'Paid' : 'Pending', $invoice_id]
            )->update();
            if ($updated) return $paymentStatus;
            return 'failed';
        } else {
            return false;
        }
    }

    public function getRequestPayments($requestId)
    {
        return $this->db->run("SELECT * FROM RequestPayments WHERE request_id = ?", [$requestId])->fetchAll();
    }

    public function getRequestPayment($id)
    {
        return $this->db->run("SELECT * FROM RequestPayments WHERE id = ?", [$id])->fetchOne();
    }

    public function payRequest($invoice_id, $amount, $payment_method)
    {
        // Assume the payment process is done and we get a response status
        $paymentStatus = 'Success'; // This should come from the payment gateway

        $result = $this->db->run(
            'INSERT INTO Payments (invoice_id, amount, payment_method, status) VALUES (?, ?, ?, ?)',
            [$invoice_id, $amount, $payment_method, $paymentStatus]
        )->insert();
        if ($result) return $paymentStatus;
        else return false;
    }
}
