# MediaStock Data Access Layer

A clean, well-encapsulated data access layer for the MediaStock database. This library provides a complete CRUD implementation for each entity in the database, with prepared statements for secure database operations.

## Installation

1. Clone this repository to your web server
2. Import the `dump.sql` file into your MySQL database
3. Update the database connection settings in `config/Database.php` if needed
4. Include the autoloader in your PHP files to use the models

## Database Structure

The database consists of the following tables:
- **Item**: Inventory items with properties like name, model, QR code, image URL, condition, and category
- **Pret**: Loans of items to borrowers, tracking loan dates, notes, and status
- **Administrateur**: Admin users with login and password
- **emprunteur**: Borrowers with name, role, and formation
- **categorie**: Item categories
- **Formation**: Training/education programs
- **sous_categorie**: Subcategories linked to main categories

## Available Models

- **Item**: Manages items in the inventory
- **Pret**: Manages loans of items to borrowers
- **Administrateur**: Manages administrators
- **Emprunteur**: Manages borrowers
- **Categorie**: Manages categories
- **Formation**: Manages formations
- **SousCategorie**: Manages subcategories

## Usage Examples

### Basic Usage

```php
// Include the autoloader
require_once 'autoload.php';

// Create a model instance
$itemModel = new Models\Item();

// Get all items
$items = $itemModel->getAll();

// Get an item by ID
$item = $itemModel->getById(1);

// Create a new item
$newItemId = $itemModel->create([
    'nom' => 'New Item',
    'model' => 'Model X',
    'qr_code' => 'QR12345',
    'image_url' => 'images/item.jpg',
    'etat' => 'bon',
    'categorie_id' => 1
]);

// Update an item
$itemModel->update(1, [
    'etat' => 'moyen'
]);

// Delete an item
$itemModel->delete(1);
```

### Advanced Usage

#### Managing Loans

```php
// Create a loan model
$pretModel = new Models\Pret();

// Create a new loan
$loanId = $pretModel->createLoan(
    $itemId,        // Item ID
    $emprunteurId,  // Borrower ID
    $adminId,       // Admin ID (lender)
    date('Y-m-d'),  // Loan date (today)
    date('Y-m-d', strtotime('+2 weeks')), // Expected return date
    'Initial condition: good'  // Initial note
);

// End a loan (item returned)
$pretModel->endLoan(
    $loanId,        // Loan ID
    date('Y-m-d'),  // Return date (today)
    'Returned in good condition'  // Final note
);

// Get active loans
$activeLoans = $pretModel->getActiveLoans();

// Get overdue loans
$overdueLoans = $pretModel->getOverdueLoans();
```

#### Working with Categories

```php
// Create a category model
$categorieModel = new Models\Categorie();

// Create a new category with subcategories
$categoryId = $categorieModel->createWithSubcategories(
    'New Category',
    ['Subcategory 1', 'Subcategory 2']
);

// Get all categories with their subcategories
$categories = $categorieModel->getAllWithSubcategories();
```

## Common Operations

### Basic CRUD Operations (available on all models)

- `getAll()` - Get all records
- `getById($id)` - Get a record by ID
- `create($data)` - Create a new record
- `update($id, $data)` - Update a record
- `delete($id)` - Delete a record
- `findBy($field, $value)` - Find records by a specific field value

### Item Operations

- `getAllWithCategory()` - Get all items with their category information
- `getWithCategory($id)` - Get an item with its category information
- `getByCategory($categoryId)` - Get items by category
- `getByCondition($condition)` - Get items by condition (état)
- `findByQrCode($qrCode)` - Find an item by QR code
- `searchByName($searchTerm)` - Search items by name
- `getAvailableItems()` - Get available items (not currently on loan)

### Loan Operations

- `getAllWithDetails()` - Get all loans with related information
- `getWithDetails($id)` - Get a specific loan with related information
- `getActiveLoans()` - Get active loans (not yet returned)
- `getOverdueLoans()` - Get overdue loans
- `getLoansByBorrower($emprunteurId)` - Get loans by borrower
- `getLoansByItem($itemId)` - Get loans by item
- `endLoan($id, $returnDate, $finalNote)` - End a loan by setting the effective return date
- `createLoan($itemId, $emprunteurId, $preteurId, $dateSortie, $dateRetourPrevue, $noteDebut)` - Create a new loan

## Security

- All database operations use prepared statements to prevent SQL injection
- Password hashing is used for administrator passwords
- Sensitive data like password hashes are not exposed in API responses

## Error Handling

The data access layer includes proper error handling with exceptions. You should wrap your code in try-catch blocks to handle any database errors that might occur.

```php
try {
    $items = $itemModel->getAll();
} catch (Exception $e) {
    // Handle the error
    echo "An error occurred: " . $e->getMessage();
}
```

## License

This project is licensed under the MIT License.