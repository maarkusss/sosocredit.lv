document.addEventListener('alpine:init', () => {
    Alpine.data('loanWidget', (loanTypes) => ({
        loanTypes,
        loanTypeId: loanTypes[0].id,
        amount: loanTypes[0].startAmount,
        stepAmount: loanTypes[0].stepAmount,
        term: loanTypes[0].startTerm,
        visible: true,

        init() {
            this.clampAmount();
            this.clampTerm();
        },

        get selectedType() {
            return this.loanTypes.find(t => t.id === this.loanTypeId) ?? this.loanTypes[0];
        },

        get monthlyRate() {
            return (this.selectedType.rate / 100) / 12;
        },

        get monthlyPayment() {
            const r = this.monthlyRate, n = this.term, p = this.amount;
            if (r === 0) return p / n;
            const factor = Math.pow(1 + r, n);
            return (p * r * factor) / (factor - 1);
        },

        get amountPercent() {
            const t = this.selectedType;
            return ((this.amount - t.min) / (t.max - t.min)) * 100;
        },

        get termPercent() {
            const t = this.selectedType;
            return ((this.term - t.minTerm) / (t.maxTerm - t.minTerm)) * 100;
        },

        clampAmount() {
            const t = this.selectedType;
            this.amount = Math.min(Math.max(this.amount, t.min), t.max);
        },

        clampTerm() {
            const t = this.selectedType;
            this.term = Math.min(Math.max(this.term, t.minTerm), t.maxTerm);
        },

        stepFor(max) {
            return this.stepAmount;
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('en-US', {
                maximumFractionDigits: 0,
            }).format(value || 0).replace(/,/g, ' ');
        },

        hide() {
            this.visible = false;
        },

        goToStep1() {
            window.dispatchEvent(new CustomEvent('loan-modal:continue', {
                detail: {
                    loanType:   this.loanTypeId,
                    loanName:   this.selectedType.label,
                    routeName:  this.selectedType.routeName,
                    amount:     this.amount,
                    term:       this.term,
                }
            }));
        },
    }));
});

window.addEventListener('loan-modal:continue', (e) => {
    const { loanType, loanName, routeName, amount, term } = e.detail;
    const locale = document.documentElement.lang || 'lv';
    const csrf   = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/${locale}/${routeName}`;

    const fields = {
        _token:      csrf,
        section_id:  loanType,
        loantype:    loanName,
        amount:      amount,
        term_months: term,
    };

    for (const [name, value] of Object.entries(fields)) {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = name;
        input.value = value;
        form.appendChild(input);
    }

    document.body.appendChild(form);
    form.submit();
});
