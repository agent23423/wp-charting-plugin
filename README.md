# Finanz Charts WordPress Plugin

## Relative Strength Index (RSI) Widget

A comprehensive financial charting plugin for WordPress with RSI, MACD, and moving average indicators.

## Features

1. *RSI Calculation Widget*
    - Real-time RSI calculation for stock prices
    - Configurable time periods (14-day default)
    - Overbought/oversold indicators (70/30 levels)
    - Responsive design for mobile/desktop

2. *MACD Indicator*
    - MACD line calculation (12-day EMA - 26-day EMA)
    - Signal line (9-day EMA of MACD line)
    - MACD histogram display
    - Buy/sell signals

3. *Moving Average Charts*
    - Simple Moving Average (SMA)
    - Exponential Moving Average (EMA)
    - Weighted Moving Average (WMA)
    - Golden Cross/Death Cross detection

4. *Real-time Gold Price Display*
    - Real-time gold price updates (XAU/USD)
    - Hourly chart display
    - Multiple currency support
    - Configurable refresh intervals

## Installation

1. Download the plugin zip file
2. Upload to your WordPress site via Plugins > Add New
#3. Activate the Finanz Charts Plugin
4. Configure the plugin settings under Settings > Finanz Charts

## Usage

1. *RSI Widget*
    ```shortcode
    [finanz_stock_rsi symbol="APPL" period="14" width="100%" height="400px"]
    ```

2. *Gold Price Display*
    ```shortcode
    [finanz_gold_price currency="USD" refresh="60"]
    ```

3. *MACD Indicator*
    ```shortcode
    [finanz_macd symbol="APPLB" fast_period="12" slow_period="26" signal_period="9"]
    ```

4. *Moving Averages*
    ```shortcode
    [finanz_moving_averages symbol="APPL" periods="7,14,21,50,100,200"]
    ```

## Technical Details

- *Frontend*: JavaScript, Chart.js
- *Backend*: PHP, WordPress REST API
- *Data Sources*: Alpha Vantage API, Yahoo Finance API
- *Caching*: Transient API
- *Responsive*: Bootstrap 5

## Development

The plugin is developed using the following tools:

1. *GitHub API*: Repository management and issue tracking
2. *WordPress REST API*: Content management and post publishing
3. *Bitwarden*: Credential management
4. *Tyk*: API gateway and security

## License

This plugin is licensed under the GPL v2 or later license.

## Contact

- *Website*: https://vyftec.com/finanz-charts-plugin-stock-market-charting-solution/
- *GitHub*: https://github.com/agent23423/wp-charting-plugin
- *Issues*: [Create new issue](https://github.com/agent23423/wp-charting-plugin/issues/new)

## Changelog

**1.0.0** (2026-02-11)
- Initial release with RSI widget
- Basic plugin structure
- Chart.js integration
- Ajax endpoints for real-time data
- Responsive design

---

Developed by Finanz Charts GmbH for the financial community.