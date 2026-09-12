<style>
    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    .diary-form {
        max-width: 850px;
    }

    fieldset {
        margin: 0 0 24px;
        padding: 20px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
    }

    legend {
        padding: 0 8px;
        font-weight: 700;
    }

    .field {
        margin-bottom: 18px;
    }

    .field:last-child {
        margin-bottom: 0;
    }

    .field>label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
    }

    input[type="text"],
    input[type="date"],
    input[type="time"],
    input[type="number"],
    textarea {
        width: 100%;
        padding: 9px 11px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 16px;
    }

    textarea {
        min-height: 100px;
        resize: vertical;
    }

    input:focus,
    textarea:focus {
        border-color: #166534;
        outline: 2px solid rgb(22 101 52 / 15%);
    }

    .choices {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
    }

    .choice {
        display: flex;
        gap: 5px;
        align-items: center;
    }

    .time-grid,
    .ammunition-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit,
                minmax(160px, 1fr));
        gap: 16px;
    }

    .error {
        margin: 6px 0 0;
        color: #dc2626;
    }

    .form-error-summary {
        margin-bottom: 20px;
        padding: 12px;
        border: 1px solid #fecaca;
        border-radius: 6px;
        background: #fef2f2;
        color: #b91c1c;
    }

    .save-button {
        padding: 12px 28px;
        border: 0;
        border-radius: 6px;
        background: #166534;
        color: #ffffff;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
    }

    .save-button:hover {
        background: #14532d;
    }

    .weather-button {
        padding: 10px 18px;
        border: 0;
        border-radius: 6px;
        background: #0369a1;
        color: #ffffff;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
    }

    .weather-button:hover {
        background: #075985;
    }

    .weather-button:disabled {
        background: #9ca3af;
        cursor: wait;
    }

    #weather-message {
        margin: 8px 0 0;
    }

    .counter {
        display: grid;
        grid-template-columns: 44px 70px 44px;
        gap: 6px;
        align-items: center;
    }

    .counter-button {
        width: 44px;
        height: 44px;
        padding: 0;
        border: 1px solid #166534;
        border-radius: 6px;
        background: #ffffff;
        color: #166534;
        font-size: 22px;
        font-weight: 700;
        cursor: pointer;
    }

    .counter-button:hover {
        background: #dcfce7;
    }

    .counter-button:disabled {
        border-color: #d1d5db;
        background: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
    }

    input.counter-value {
        width: 70px;
        height: 44px;
        padding: 4px;
        background: #ffffff;
        text-align: center;
        font-size: 18px;
        font-weight: 700;
    }

    .counter-value::-webkit-inner-spin-button,
    .counter-value::-webkit-outer-spin-button {
        margin: 0;
        appearance: none;
    }

    .counter-unit {
        width: 164px;
        margin: 4px 0 0;
        color: #4b5563;
        text-align: center;
        font-size: 13px;
    }

    @media (max-width: 600px) {

        .time-grid,
        .ammunition-grid {
            grid-template-columns: 1fr;
        }

        fieldset {
            padding: 14px;
        }

        .save-button {
            width: 100%;
        }
    }
</style>