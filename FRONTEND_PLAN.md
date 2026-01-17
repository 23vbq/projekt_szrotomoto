# Frontend Application Plan - Szrotomoto

## Overview
The frontend is a **server-rendered PHP application** with **client-side JavaScript** for interactivity. It follows a traditional multi-page application (MPA) architecture with progressive enhancement.

## Architecture

### Technology Stack
- **Server-side**: PHP 8.4 (for page rendering and includes)
- **Client-side**: Vanilla JavaScript (ES6+)
- **Styling**: CSS (custom stylesheet)
- **API Communication**: Fetch API with custom `apiFetch` wrapper
- **Authentication**: Session-based (cookies managed by browser)

### Current Structure
```
frontend/
├── _nav.php              # Navigation component (shared header)
├── login.php             # Login page
├── register.php          # Registration page
├── offers.php            # Offers listing page
├── offer.php             # Offer detail page
├── offers_create.php     # Create offer form
└── assets/
    ├── css/
    │   └── style.css     # Global styles
    └── js/
        └── api.js        # API fetch utility
```

## Page-by-Page Functionality

### 1. Navigation (`_nav.php`)
**Purpose**: Shared header component across all pages

**Features**:
- ✅ Brand logo linking to home/offers
- ✅ Navigation links (Browse offers, Create offer)
- ✅ Authentication state detection via `/api/login/me.php`
- ✅ Dynamic UI based on auth state:
  - **Unauthenticated**: Show "Login" and "Register" links
  - **Authenticated**: Show user name, logout button
- ✅ Logout functionality

**Implementation Notes**:
- Uses async script to check auth state on page load
- Updates UI dynamically based on response
- Handles logout with redirect to offers page

---

### 2. Offers Listing (`offers.php`)
**Purpose**: Display all active vehicle offers with filtering and sorting

**Features**:
- ✅ Fetch and display all active offers from `/api/offers/search.php`
- ✅ Client-side filtering by:
  - Brand (with cascade to models)
  - Model
  - Fuel type
  - Transmission type
  - Body type
- ✅ Client-side sorting by:
  - Price (asc/desc)
  - Odometer (asc/desc)
  - Production year (asc/desc)
- ✅ Offer cards showing:
  - Title with brand/model
  - Thumbnail image (first attachment)
  - Key details (year, mileage, price)
  - Link to detail page
- ✅ "Create new offer" link (visible to all, but backend enforces auth)

**Data Flow**:
1. On page load: Fetch offers + brands + fuel types + transmission types + body types (parallel)
2. Populate filter dropdowns
3. Wire up brand → models cascade (fetch models when brand selected)
4. Render initial offers list
5. Apply filters/sort on user interaction

**UI Components**:
- Collapsible filters panel
- Sort dropdown
- Apply/Clear filter buttons
- Responsive offer grid/list

---

### 3. Offer Detail (`offer.php`)
**Purpose**: Display full details of a single offer

**Features**:
- ✅ Fetch offer by ID from `/api/offers/show.php?offer_id={id}`
- ✅ Display all offer information:
  - Title, brand, model
  - Price, production year, odometer
  - Description
  - Technical specs (fuel, transmission, displacement, horsepower, etc.)
  - Vehicle details (doors, seats, VIN, registration, country)
  - Seller name
- ✅ Image gallery (all attachments)
- ✅ Action buttons (only for offer creator):
  - "Set as sold" → `/api/offers/setAsSold.php`
  - "Remove offer" → `/api/offers/setAsRemoved.php`
- ✅ Back to offers link

**Authorization**:
- Backend handles: Only creator can see action buttons (403 if not authorized)
- Frontend shows buttons to all, but backend validates

**Error Handling**:
- Handle missing offer_id parameter
- Handle 404 (offer not found)
- Handle inactive offers (only creator can view)

---

### 4. Create Offer (`offers_create.php`)
**Purpose**: Form to create a new vehicle offer

**Features**:
- ✅ Authentication required (backend enforces)
- ✅ Form fields:
  - **Required**: Brand, Model, Title, Price, Production year, Odometer, Fuel type, Transmission, Body type, VIN
  - **Optional**: Description, Color, Displacement, Horsepower, Torque, Doors, Seats, Registration number, Country, Boolean flags (accident-free, first-hand, used, warranty, service book)
- ✅ Dynamic brand → model cascade
- ✅ File upload for images (multiple files)
- ✅ Validation:
  - Client-side: Required fields, file types
  - Server-side: All validations (enum values, VIN uniqueness, etc.)
- ✅ Success: Redirect to offers list after creation

**Data Flow**:
1. Load static values (fuel types, transmission types, body types, brands)
2. User selects brand → fetch models for that brand
3. User fills form and selects images
4. Submit as `multipart/form-data` to `/api/offers/create.php`
5. Handle success/error response

**Form Structure**:
- Uses native HTML form with `enctype="multipart/form-data"`
- FormData API for submission
- File input with `multiple` attribute

---

### 5. Login (`login.php`)
**Purpose**: User authentication

**Features**:
- ✅ Form with email and password
- ✅ Validation: Email format (client-side)
- ✅ Submit to `/api/login/login.php`
- ✅ Success: Store session cookie, redirect to offers
- ✅ Error handling: Display error messages
- ✅ Link to registration page

**Implementation**:
- Uses `URLSearchParams` for form data (not JSON)
- Session cookie set automatically by backend
- Redirect after successful login

---

### 6. Register (`register.php`)
**Purpose**: New user registration

**Features**:
- ✅ Form with: Name, Email, Password, Repeat password
- ✅ Validation:
  - Client-side: Required fields, email format, password match
  - Server-side: Email format, password match, email uniqueness
- ✅ Submit to `/api/login/register.php`
- ✅ Success: Redirect to login page
- ✅ Error handling: Display specific error messages
- ✅ Link to login page

**Error Messages**:
- Missing required fields
- Invalid email format
- Passwords do not match
- Email already registered

---

## API Integration Layer

### `api.js` - API Fetch Utility

**Purpose**: Centralized API communication with error handling

**Features**:
- ✅ `apiFetch(path, options)` function
- ✅ Automatic JSON parsing when Content-Type is JSON
- ✅ Error handling: Network errors, HTTP errors
- ✅ Returns standardized response: `{ ok: boolean, status: number, data: any, error: string }`
- ✅ Supports FormData and URLSearchParams for POST requests
- ✅ Credentials: `same-origin` (sends cookies for session)

**Usage Pattern**:
```javascript
const res = await apiFetch('/api/endpoint.php', { 
  method: 'POST', 
  body: formData 
});

if (res.ok) {
  // Handle success
} else {
  // Handle error: res.error
}
```

---

## User Experience Flow

### Unauthenticated User
1. **Landing**: Arrives at offers listing
2. **Browse**: Can view all active offers, filter, sort
3. **View Details**: Can view offer details (read-only)
4. **Create Offer**: Clicking "Create offer" → Redirected to login
5. **Register/Login**: Can create account or login
6. **After Login**: Redirected to offers, can now create offers

### Authenticated User
1. **Navigation**: Sees user name in header
2. **Browse Offers**: Same as unauthenticated
3. **Create Offer**: Can access create form, submit with images
4. **Own Offers**: Can see "Set as sold" and "Remove" buttons on own offers
5. **Edit Offer**: (Not yet implemented - see Future Enhancements)
6. **Logout**: Can logout, returns to unauthenticated state

---

## State Management

### Client-Side State
- **No global state management** (no Redux/Vuex)
- **Page-level state**: Each page manages its own state
- **Cache**: Offers list cached in memory for filtering/sorting
- **Session state**: Managed by backend (cookies)

### Data Caching Strategy
- **Offers**: Cached in `offersCache` array on offers page
- **Static values**: Fetched once per page load (brands, fuel types, etc.)
- **Models**: Fetched on-demand when brand selected

---

## Error Handling

### Network Errors
- Display user-friendly error messages
- Fallback UI when API unavailable
- Retry logic (not implemented, but could be added)

### Validation Errors
- **Client-side**: Immediate feedback on form fields
- **Server-side**: Display error messages from API response
- **Field-level errors**: Could be enhanced to show per-field errors

### Authentication Errors
- 401 Unauthorized: Redirect to login
- 403 Forbidden: Show error message (e.g., "Not authorized to edit this offer")

---

## Styling & UI

### Design Principles
- **Responsive**: Works on mobile and desktop
- **Accessible**: Semantic HTML, ARIA labels where needed
- **Clean**: Minimal, functional design
- **Consistent**: Shared navigation and styling across pages

### CSS Structure
- Global stylesheet (`assets/css/style.css`)
- Utility classes (`.container`, `.muted`, `.primary`, `.secondary`, `.danger`)
- Component styles (`.offer-card`, `.nav-auth`, etc.)

---

## Security Considerations

### Client-Side
- ✅ Input validation (but not trusted - backend validates)
- ✅ HTTPS recommended (handled by infrastructure)
- ✅ XSS prevention: No innerHTML with user content (use textContent where possible)
- ⚠️ **Note**: Some pages use innerHTML for dynamic content - should sanitize

### Authentication
- ✅ Session-based (secure, HTTP-only cookies recommended)
- ✅ Credentials sent with `same-origin`
- ✅ Backend validates all authenticated requests

### File Uploads
- ✅ File type validation (accept attribute)
- ✅ Backend validates MIME types and file size
- ✅ Unique filenames prevent overwrites

---

## Performance Optimizations

### Current
- ✅ Parallel API calls (Promise.all) for initial data loading
- ✅ Client-side filtering (no server round-trip)
- ✅ Image lazy loading (could be added)

### Potential Improvements
- Image optimization (thumbnails, WebP)
- Pagination for offers list (currently loads all)
- Debouncing for filter inputs
- Service worker for offline support (PWA)

---

## Future Enhancements

### High Priority
1. **Edit Offer Page** (`offers_edit.php`)
   - Similar to create, but pre-populated
   - Update existing offer via `/api/offers/edit.php`
   - Handle file additions (append to existing attachments)

2. **User Profile Page**
   - Display user's offers
   - Edit profile information
   - Change password

3. **Search Functionality**
   - Server-side search (currently client-side filtering only)
   - Full-text search on title/description
   - Advanced filters (price range, year range)

4. **Pagination**
   - Server-side pagination for offers list
   - Load more / infinite scroll option

### Medium Priority
5. **Image Gallery Improvements**
   - Lightbox/modal for full-size images
   - Image carousel/slider
   - Image reordering (drag-and-drop)

6. **Form Enhancements**
   - Auto-save draft offers
   - Form validation with better UX (inline errors)
   - Character counters for text fields

7. **Notifications**
   - Success/error toast notifications
   - Confirmation dialogs for destructive actions

8. **Responsive Design**
   - Mobile-first approach
   - Touch-friendly interactions
   - Mobile navigation menu

### Low Priority
9. **Favorites/Watchlist**
   - Save favorite offers
   - Get notified of price changes

10. **Advanced Filtering**
    - Price range slider
    - Year range
    - Multiple selections (OR conditions)

11. **Sorting Options**
    - Sort by date (newest/oldest)
    - Sort by relevance (if search implemented)

12. **Offer Comparison**
    - Compare multiple offers side-by-side

---

## Testing Considerations

### Manual Testing Checklist
- [ ] User registration flow
- [ ] Login/logout functionality
- [ ] Create offer with images
- [ ] View offer details
- [ ] Filter and sort offers
- [ ] Edit own offer (when implemented)
- [ ] Set offer as sold
- [ ] Remove offer
- [ ] Error handling (network errors, validation errors)
- [ ] Responsive design (mobile/tablet/desktop)

### Automated Testing (Future)
- Unit tests for JavaScript utilities
- Integration tests for API calls
- E2E tests for critical user flows

---

## Deployment Considerations

### Current Setup
- Docker container with PHP 8.4 + Apache
- Served on port 80 (frontend)
- Backend API on port 3000
- Shared network for API communication

### Production Recommendations
- HTTPS/SSL certificates
- CDN for static assets
- Image optimization pipeline
- Error logging and monitoring
- Rate limiting (backend)
- CORS configuration (if needed for separate domains)

---

## Code Organization Best Practices

### Current Structure
- ✅ Separation of concerns (PHP for rendering, JS for interactivity)
- ✅ Reusable components (`_nav.php`)
- ✅ Centralized API utility

### Recommendations
1. **JavaScript Modules**
   - Consider splitting large inline scripts into modules
   - Create reusable components (e.g., `OfferCard`, `FilterPanel`)

2. **CSS Organization**
   - Consider CSS variables for theming
   - Component-based CSS structure
   - Mobile-first media queries

3. **Error Handling**
   - Centralized error display component
   - Consistent error message formatting

4. **Loading States**
   - Show loading indicators during API calls
   - Skeleton screens for better UX

---

## Summary

The frontend is a **traditional server-rendered multi-page application** that:
- Uses PHP for page structure and includes
- Uses vanilla JavaScript for client-side interactivity
- Communicates with backend via RESTful API
- Manages authentication via session cookies
- Provides a clean, functional UI for browsing and managing vehicle offers

The architecture is simple, maintainable, and suitable for the current scope. Future enhancements can be added incrementally without major refactoring.

