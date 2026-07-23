/**
 * API utility functions that can be used in both client and server components
 */

/**
 * Validates the API key by checking if the user is authenticated
 */
export async function validateApiKey(): Promise<{ valid: boolean; token?: string; error?: string }> {
  // In a client component, check for stored auth token
  if (typeof window !== 'undefined') {
    try {
      const token = localStorage.getItem('sg_token');
      if (!token) {
        return { valid: false, error: 'No authentication token found' };
      }
      
      // Here you could also verify the token with your backend if needed
      return { valid: true, token };
    } catch (error) {
      console.error('Error validating API key:', error);
      return { valid: false, error: 'Error validating authentication' };
    }
  }
  
  // On the server side, this would work differently
  // For now, return a basic response
  return { valid: false, error: 'Not running in browser environment' };
}

/**
 * Handles API errors consistently
 */
export function handleApiError(error: any): Response {
  console.error('API Error:', error);
  
  const status = error.status || 500;
  const message = error.message || 'Internal Server Error';
  
  return new Response(
    JSON.stringify({ 
      success: false, 
      error: message,
      ...(process.env.NODE_ENV === 'development' && { stack: error.stack })
    }), 
    { 
      status,
      headers: { 'Content-Type': 'application/json' } 
    }
  );
}