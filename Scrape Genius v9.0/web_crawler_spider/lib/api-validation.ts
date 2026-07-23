import { NextResponse } from 'next/server';

interface ApiKeyValidationResult {
  valid: boolean;
  token?: string;
  error?: string;
}

/**
 * Validates the API key by making a request to the backend
 * This function can be called from both server and client components
 */
export async function validateApiKey(): Promise<ApiKeyValidationResult> {
  try {
    // In a real implementation, this would call an API endpoint to validate the key
    // For now, we'll simulate the validation
    const token = typeof window !== 'undefined' ? localStorage.getItem('sg_token') : null;
    
    if (!token) {
      return { valid: false, error: 'No authentication token found' };
    }
    
    // Here we could make an actual API call to validate the token
    // For now, we'll just return a valid response
    return { valid: true, token };
  } catch (error) {
    console.error('API key validation error:', error);
    return { valid: false, error: (error as Error).message };
  }
}

/**
 * Handles API errors consistently across the application
 * This function can be used in server components
 */
export function handleApiError(error: any): NextResponse {
  console.error('API Error:', error);
  
  // Determine the appropriate status code
  const statusCode = error.status || 500;
  const errorMessage = error.message || 'Internal Server Error';
  
  return NextResponse.json(
    { 
      success: false, 
      error: errorMessage,
      ...(process.env.NODE_ENV === 'development' && { stack: error.stack })
    },
    { status: statusCode }
  );
}