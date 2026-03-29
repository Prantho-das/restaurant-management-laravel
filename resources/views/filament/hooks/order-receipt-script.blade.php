<script>
    window.addEventListener("print-order-receipt", event => {
        const receipt = event.detail.receipt;
        if (!receipt) return;
        window.printPosReceipt(receipt);
    });

    window.printPosReceipt = function (r) {
        const payLabel = { cash: "Cash", card: "Card", mobile_pay: "bKash/MFS", bkash: "bKash", sslcommerze: "SSLCommerze" }[r.payment_method] || r.payment_method;
        const typeLabel = { dine_in: "Dine-In", takeaway: "Takeaway", delivery: "Delivery" }[r.order_type] || r.order_type;

        const itemsHtml = r.items.map(i =>
            `<tr>
                <td style="padding:2px 0;word-break:break-word">${i.name}</td>
                <td style="text-align:center;padding:2px 4px">${i.qty}</td>
                <td style="text-align:right;padding:2px 0">${Number(i.price).toFixed(0)}</td>
                <td style="text-align:right;padding:2px 0">${Number(i.subtotal).toFixed(0)}</td>
            </tr>`
        ).join("");

        const discountRow = r.discount > 0
            ? `<tr><td colspan="3" style="text-align:right;padding:2px 0">Discount ${r.discount_type === "percentage" ? "(" + r.discount_value + "%)" : ""}</td><td style="text-align:right;padding:2px 0">-${Number(r.discount).toFixed(0)}</td></tr>`
            : "";

        const tableRow = (r.order_type === "dine_in" && r.table_number) ? `<div>Table: <b>${r.table_number}</b></div>` : "";
        const refRow = r.reference_no ? `<div>Ref: ${r.reference_no}</div>` : "";
        const addressRow = r.restaurant_address ? `<div>${r.restaurant_address}</div>` : "";
        const phoneRow = r.restaurant_phone ? `<div>${r.restaurant_phone}</div>` : "";

        const html = `
            <!DOCTYPE html><html><head><meta charset="UTF-8"><title>Receipt - ${r.order_number}<\/title>
            <style>
                * { margin:0;padding:0;box-sizing:border-box; }
                body { font-family:"Courier New",Courier,monospace;font-size:11px;width:80mm;margin:0 auto;color:#000; }
                .center { text-align:center; } .bold { font-weight:bold; } .big { font-size:15px; }
                .divider { border-top:1px dashed #000;margin:5px 0; }
                table { width:100%;border-collapse:collapse; }
                th { font-size:10px;border-bottom:1px dashed #000;padding-bottom:3px; }
                .total-row td { border-top:1px dashed #000;padding-top:4px;font-weight:bold;font-size:13px; }
                .footer { margin-top:8px;text-align:center;font-size:10px; }
                @media print { @page { size: 80mm auto; margin: 4mm; } body { width:78mm; } }
            <\/style><\/head><body>
            <div class="center"><div class="bold big">${r.restaurant_name}<\/div>${addressRow}${phoneRow}<\/div>
            <div class="divider"><\/div>
            <div class="center bold" style="font-size:13px">** RECEIPT **<\/div>
            <div class="divider"><\/div>
            <div>Order: <b>${r.order_number}<\/b><\/div>
            <div>Date: ${r.datetime}<\/div>
            <div>Cashier: ${r.cashier}<\/div>
            <div>Type: <b>${typeLabel}<\/b><\/div>
            ${tableRow}
            <div>Customer: ${r.customer_name}<\/div>
            <div>Payment: <b>${payLabel}<\/b><\/div>
            ${refRow}
            <div class="divider"><\/div>
            <table><thead><tr><th style="text-align:left">Item<\/th><th style="text-align:center">Qty<\/th><th style="text-align:right">Price<\/th><th style="text-align:right">Total<\/th><\/tr><\/thead>
            <tbody>${itemsHtml}<\/tbody>
            <tfoot>
            <tr><td colspan="3" style="text-align:right;padding-top:4px">Subtotal<\/td><td style="text-align:right;padding-top:4px">${Number(r.subtotal).toFixed(0)}<\/td><\/tr>
            ${discountRow}
            <tr class="total-row"><td colspan="3" style="text-align:right">TOTAL<\/td><td style="text-align:right">৳${Number(r.total).toFixed(0)}<\/td><\/tr>
            <\/tfoot><\/table>
            <div class="divider"><\/div>
            <div class="footer"><div>Thank you for dining with us!<\/div><div style="margin-top:4px;font-size:9px">Powered by ${r.restaurant_name} POS<\/div><\/div>
            <\/body><\/html>`;

        const win = window.open("", "_blank", "width=340,height=600,toolbar=0,scrollbars=1,status=0");
        if (!win) {
            alert("Pop-up blocked. Please allow pop-ups for this site to print receipts.");
            return;
        }
        win.document.write(html);
        win.document.close();
        win.focus();
        setTimeout(() => { win.print(); }, 400);
    }
</script>
