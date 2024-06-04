<?php

namespace Controller;

class Invoice
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getInvoices()
    {
        return $this->db->run("SELECT * FROM Invoices")->fetchAll();
    }

    public function getInvoice($id)
    {
        return $this->db->run(
            "SELECT * FROM Invoices WHERE id = ?",
            [$id]
        )->fetchOne();
    }

    public function getByUserId($user_id)
    {
        return $this->db->run('SELECT * FROM Invoices WHERE user_id = ?', [$user_id])->fetchAll();
    }

    public function createInvoice($data)
    {
        $query = "INSERT INTO Invoices (customer_id, branch_id, total, discount, status) VALUES (?, ?, ?, ?, ?)";
        $params = [
            $data['customer_id'],
            $data['branch_id'],
            $data['total'],
            $data['discount'],
            $data['status']
        ];
        return $this->db->run($query, $params)->insert();
    }

    public function create($request_id, $items)
    {
        $totalCost = 0;

        foreach ($items as $item) {
            $stmt = $this->db->run(
                'SELECT unit_cost FROM Items WHERE id = ?',
                [$item['item_id']]
            )->fetchOne();
            $itemCost = $stmt->fetchColumn();
            $totalCost += $itemCost * $item['quantity'];
        }

        $invoiceId = $this->db->run(
            'INSERT INTO Invoices (request_id, total_cost, status) VALUES (?, ?, ?)',
            [$request_id, $totalCost, 'Pending']
        )->insert(true);

        if ($invoiceId) {
            foreach ($items as $item) {
                $stmt = $this->db->run(
                    'INSERT INTO LaundryItems (request_id, item_id, quantity) VALUES (?, ?, ?)',
                    [$request_id, $item['item_id'], $item['quantity']]
                )->insert();
            }
            return $invoiceId;
        } else {
            return false;
        }
    }

    public function updateInvoice($id, $data)
    {
        $query = "UPDATE Invoices SET customer_id = ?, branch_id = ?, total = ?, discount = ?, status = ? WHERE id = ?";
        $params = [
            $data['customer_id'],
            $data['branch_id'],
            $data['total'],
            $data['discount'],
            $data['status']
        ];
        return $this->db->run($query, $params)->update();
    }

    public function deleteInvoice($id)
    {
        return $this->db->run("DELETE FROM Invoices WHERE id = ?", [$id])->delete();
    }
}
