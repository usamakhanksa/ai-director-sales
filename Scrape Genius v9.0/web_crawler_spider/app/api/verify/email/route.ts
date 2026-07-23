import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import dns from "dns";

// Validation schema for email verification
const emailVerificationSchema = z.object({
  email: z.string().email({ message: "Email must be a valid email address" }),
});

export async function POST(req: NextRequest): Promise<NextResponse> {
  try {
    // Parse and validate the request body
    const body = await req.json();
    const validatedData = emailVerificationSchema.parse(body);
    
    const { email } = validatedData;
    
    // Extract domain from email
    const domain = email.split('@')[1];
    
    // Perform MX record lookup to check if domain accepts emails
    let mxRecords: dns.MxRecord[] | undefined;
    let hasMxRecord = false;
    
    try {
      mxRecords = await new Promise((resolve, reject) => {
        dns.resolveMx(domain, (err, addresses) => {
          if (err) {
            // If MX lookup fails, try resolving A record as fallback
            return resolve(undefined);
          }
          resolve(addresses);
        });
      });
      
      hasMxRecord = mxRecords !== undefined && mxRecords.length > 0;
    } catch (error) {
      // If MX lookup fails, consider it as no MX record but continue with other checks
      hasMxRecord = false;
    }
    
    // Additional fallback: try to resolve A record if no MX record exists
    if (!hasMxRecord) {
      try {
        await new Promise((resolve, reject) => {
          dns.resolve4(domain, (err, addresses) => {
            if (err) {
              return resolve(null);
            }
            resolve(addresses);
          });
        });
        // If A record resolution succeeds, we consider it a positive sign
        hasMxRecord = true;
      } catch (error) {
        // Domain doesn't have A record either, so no email delivery possible
        hasMxRecord = false;
      }
    }
    
    // Perform additional checks
    const isDisposable = isDisposableEmail(email);
    const isFreeProvider = isFreeEmailProvider(email);
    
    return NextResponse.json({
      success: true,
      data: {
        email,
        isValidSyntax: true,
        hasMxRecord,
        isDisposable,
        isFreeProvider,
        deliverable: hasMxRecord && !isDisposable, // Consider deliverable if MX exists and not disposable
        suggestions: generateSuggestions(email),
      },
      message: hasMxRecord && !isDisposable 
        ? "Email appears to be valid and deliverable" 
        : hasMxRecord 
          ? "Email syntax is valid but is from a disposable provider" 
          : "Email syntax is valid but domain does not appear to accept emails",
    });
  } catch (error) {
    if (error instanceof z.ZodError) {
      return NextResponse.json(
        { 
          success: false, 
          error: "Invalid input", 
          details: error.errors 
        },
        { status: 400 }
      );
    }
    
    console.error("Email verification error:", error);
    return NextResponse.json(
      { 
        success: false, 
        error: "Internal server error" 
      },
      { status: 500 }
    );
  }
}

// Helper function to detect disposable email providers
function isDisposableEmail(email: string): boolean {
  const disposableDomains = [
    '10minutemail.com', 'tempmail.org', 'guerrillamail.com', 'sharklasers.com',
    'mailinator.com', 'yopmail.com', 'trashmail.com', 'throwawaymail.com',
    'disposablemail.com', 'getairmail.com', 'grr.la', 'yandex.com',
    'temp-mail.org', 'temp-mail.ru', 'guerrillamailblock.com', '10minutemail.net',
    'tempinbox.com', 'fakeinbox.com', 'trashmail.ws', 'maildrop.cc'
  ];
  
  const domain = email.split('@')[1]?.toLowerCase();
  return domain ? disposableDomains.some(d => domain.endsWith(d)) : false;
}

// Helper function to detect free email providers
function isFreeEmailProvider(email: string): boolean {
  const freeProviders = [
    'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'live.com',
    'aol.com', 'icloud.com', 'protonmail.com', 'zoho.com', 'mail.com',
    'yandex.com', 'qq.com', '163.com', '126.com'
  ];
  
  const domain = email.split('@')[1]?.toLowerCase();
  return domain ? freeProviders.some(provider => domain === provider) : false;
}

// Helper function to generate email correction suggestions
function generateSuggestions(email: string): string[] {
  const suggestions: string[] = [];
  
  // Common typos in popular domains
  const domain = email.split('@')[1]?.toLowerCase();
  const localPart = email.split('@')[0];
  
  if (domain === 'gamil.com' || domain === 'gmial.com' || domain === 'gmai.com') {
    suggestions.push(`${localPart}@gmail.com`);
  } else if (domain === 'yaho.com' || domain === 'yaoo.com') {
    suggestions.push(`${localPart}@yahoo.com`);
  } else if (domain === 'hotmal.com' || domain === 'hotmial.com') {
    suggestions.push(`${localPart}@hotmail.com`);
  } else if (domain === 'outlok.com' || domain === 'outloook.com') {
    suggestions.push(`${localPart}@outlook.com`);
  }
  
  return suggestions;
}